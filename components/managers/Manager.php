<?php

namespace app\components\managers;

use Yii;
use app\models\Product;
use app\models\Category;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\base\Exception;

class Manager {

    public function updateProductCategories($existingValues, $postNewCategories, $product, $productId) {
        $deleteList = [];
        $newIndexList = [];

        if (!empty($postNewCategories)) {
            $deleteList = self::getDeleteList($existingValues, $postNewCategories);
            $newIndexList = self::getNewIndexList($existingValues, $postNewCategories);
        }

        $dbTransaction = Yii::$app->db->beginTransaction();
        try {
            if (!empty($deleteList)) {
                Yii::$app->db
                        ->createCommand()
                        ->delete('CategoryMap', ['product_id' => $productId, 'category_id' => $deleteList])
                        ->execute();
            } else {
                Yii::$app->db
                        ->createCommand()
                        ->delete('CategoryMap', ['product_id' => $productId])
                        ->execute();
            }

            foreach ($newIndexList as $categoryId) {
                $category = Category::findOne($categoryId);
                $product->link('categories', $category);
            }
            $dbTransaction->commit();
            return true;
        } catch (Exception $ex) {
            $dbTransaction->rollBack();
            return false;
        }
    }

    public function createProductWithCategory($newProduct) {
        $product = new Product();
        try {
            $dbTransaction = Yii::$app->db->beginTransaction();
            if ($product->load($newProduct)) {
                $imageName = strtolower($product->name);
                $product->file = UploadedFile::getInstance($product, 'file');
                // If we do not have picture upload default
                if (empty($product->file)) {
                    $product->picture = $product->DefaulPicture;
                } else {
                    // else save new picture
                    $product->picture = self::setNewPicture($imageName, $product);
                }
                if ($product->save()) {
                    // If categories are selected we add them to junktion table
                    if (!empty($newProduct['Product']['category'])) {
                        foreach ($newProduct['Product']['category'] as $categoryId) {
                            $category = Category::findOne($categoryId);
                            $product->link('categories', $category);
                        }
                    }
                } else {
                    $dbTransaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', 'Product save failed !!!');
                    return false;
                }
            } else {
                $dbTransaction->rollBack();
                Yii::$app->getSession()->setFlash('error', 'Product validation failed !!!');
                return false;
            }
            // if we have new picture we add it to table
            if (!empty($product->file)) {
                $product->file->saveAs($product->picture);
            }
            $dbTransaction->commit();
            return $product->id;
        } catch (Exception $e) {
            $dbTransaction->rollBack();
            return false;
        }
    }

    public static function getDeleteList($existingValues, $postNewCategories) {
        $deleteList = [];
        // Rows that we no longer need
        foreach ($existingValues as $value) {
            if (!in_array($value, $existingValues)) {
                $deleteList[] = $value;
            }
        }

        return $deleteList;
    }

    public static function getNewIndexList($existingValues, $postNewCategories) {
        $newIndexList = [];

        //Rows that we will need to create
        foreach ($postNewCategories as $value) {
            if (!in_array($value, $existingValues)) {
                $newIndexList[] = $value;
            }
        }

        return $newIndexList;
    }

    public static function setNewPicture($imageName, $product) {
        $randNumber = mt_rand(10, 1000);
        $tempImageName = 'images/products/' . $imageName . $randNumber . '.' . $product->file->extension;
        $files = FileHelper::findFiles('images/products/');

        while (true) {
            if (self::verifyPictureName($tempImageName, $files)) {
                return $tempImageName;
            } else {
                $randNumber = mt_rand(10, 1000);
                $previousName = explode('.', $tempImageName);
                $previousName[0] .= '/' . $randNumber . '.' . $product->file->extension;
                $tempImageName = $previousName[0];
            }
        }
    }

    protected static function verifyPictureName($tempImageName, $files) {
        foreach ($files as $picture) {
            if ($picture == $tempImageName) {
                return false;
            }
        }
        return true;
    }

}
