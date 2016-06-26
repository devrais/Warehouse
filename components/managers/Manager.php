<?php

namespace app\components\managers;

use Yii;
use app\models\CategoryMap;
use app\models\Product;
use yii\web\UploadedFile;

class Manager {

    public function updateProductCategories($existingValues, $postValues, $productId) {
        $map = new CategoryMap();
        $deleteList = [];
        $newIndexList = [];
        $postNewMap = [];

        $deleteList = self::getDeleteList($existingValues, $postValues);
        $newIndexList = self::getNewIndexList($existingValues, $postValues);

        $dbTransaction = Yii::$app->db->beginTransaction();
        try {
            if (!empty($deleteList)) {
                Yii::$app->db
                        ->createCommand()
                        ->delete('CategoryMap', ['product_id' => $productId, 'category_id' => $deleteList])
                        ->execute();
            }

            foreach ($newIndexList as $value) {
                $postNewMap['CategoryMap']['product_id'] = $productId;
                $postNewMap['CategoryMap']['category_id'] = $value;
                $postNewMap['_csrf'] = Yii::$app->request->csrfToken;
                if ($map->load($postNewMap) && $map->save()) {
                    $map = new CategoryMap();
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Failed to save category ' . $map->name);
                    $dbTransaction->rollBack();
                }
            }
            $dbTransaction->commit();
            return $productId;
        } catch (Exception $ex) {
            $dbTransaction->rollBack();
            return false;
        }
    }

    public function updateProduct($updatedProduct, $product) {
        
        $previousPicture = $product->picture;
        if (empty(UploadedFile::getInstance($product, 'file'))) {
            $updatedProduct['Product']['picture'] = $previousPicture;

            if ($product->load($updatedProduct) && $product->save()) {
                return $product->id;
            } else {
                return false;
            }
        } else {
            $newFile = UploadedFile::getInstance($product, 'file');
            $ext = pathinfo($newFile, PATHINFO_EXTENSION);
            $randNumber = mt_rand(10, 1000);
            $imageName = strtolower($product->name);
            //save the path in the db column
            $updatedProduct['Product']['picture'] = 'images/products/' . $imageName . $randNumber . '.' . $ext;

            if ($product->load($updatedProduct) && $product->save()) {
                $product->file = $newFile;
                $product->file->saveAs($updatedProduct['Product']['picture']);

                //   $product->file->saveAs('images/products/' . $imageName . $randNumber . '.' . $product->file->extension);
                if ($previousPicture != '/images/products/default-product.jpg') {
                    unlink(Yii::$app->basePath . '/web/' . $previousPicture);
                }
                return $product->id;
            } else {
                return false;
            }
        }
    }

    public function createProductWithCategory($newProduct, $newMap, $categories) {

        $product = new Product();
        $map = new CategoryMap();

        try {
            $dbTransaction = Yii::$app->db->beginTransaction();
            if ($product->load($newProduct)) {
                //get the instance of the uploaded file
                $imageName = strtolower($product->name);
                $product->file = UploadedFile::getInstance($product, 'file');
                if (empty($product->file)) {
                    $product->picture = $product->DefaulPicture;
                } else {
                    $randNumber = mt_rand(10, 1000);
                    //save the path in the db column
                    $product->picture = 'images/products/' . $imageName . $randNumber . '.' . $product->file->extension;
                }

                if ($product->save()) {
                    $newMap['CategoryMap']['product_id'] = Yii::$app->db->getLastInsertID();
                    foreach ($categories as $categoryId) {
                        $newMap['CategoryMap']['category_id'] = $categoryId;
                        if ($map->load($newMap) && $map->save()) {
                            $map = new CategoryMap();
                        } else {
                            Yii::$app->getSession()->setFlash('error', 'Failed to save product/category relation ');
                            $dbTransaction->rollBack();
                            return false;
                        }
                    }
                } else {
                    $dbTransaction->rollBack();
                    return false;
                }
            } else {
                $dbTransaction->rollBack();
                Yii::$app->getSession()->setFlash('error', 'Product validation failed !!!');
                return false;
            }

            if (!empty($product->file)) {
                $product->file->saveAs('images/products/' . $imageName . $randNumber . '.' . $product->file->extension);
            }
            $dbTransaction->commit();
            return $product->id;
        } catch (Exception $e) {
            $dbTransaction->rollBack();
            return false;
        }
    }

    public static function getDeleteList($existingValues, $postValues) {
        $deleteList = [];
        // Rows that we no longer need
        foreach ($existingValues as $value) {
            if (!in_array($value, $postValues)) {
                $deleteList[] = $value;
            }
        }

        return $deleteList;
    }

    public static function getNewIndexList($existingValues, $postValues) {
        $newIndexList = [];

        //Rows that we will need to create
        foreach ($postValues as $value) {
            if (!in_array($value, $existingValues)) {
                $newIndexList[] = $value;
            }
        }

        return $newIndexList;
    }

}
