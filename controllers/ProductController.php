<?php

namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\Category;
use app\models\CategoryMap;
use app\models\ProductSearch;
use app\components\managers\Manager;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
       $category = new Category();
       $product = new Product();
       $map = new CategoryMap();
       $postData = Yii::$app->request->post();

        if (isset($postData['_csrf']) && isset($postData['Product']) && isset($postData['Category'])) {
            $newProduct['_csrf'] = $postData['_csrf'];
            $newProduct['Product'] = $postData['Product'];
            $newMap['_csrf'] = $postData['_csrf'];
            $categories = $postData['Category']['name'];
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

                    $product->save();
                    $newMap['CategoryMap']['product_id'] = Yii::$app->db->getLastInsertID();

                    foreach ($categories as $categoryId) {
                        $newMap['CategoryMap']['category_id'] = $categoryId;
                        if ($map->load($newMap)) {
                            $map->save();
                            $map = new CategoryMap();
                        } else {
                            $dbTransaction->rollBack();
                            echo 'MAP fail';
                            exit();
                        }
                    }
                } else {
                    $dbTransaction->rollBack();
                    echo 'Product fail';
                    exit();
                }
                
                if(!empty($product->file))
                {
                      $product->file->saveAs('images/products/' . $imageName . $randNumber . '.' . $product->file->extension);
                }
                $dbTransaction->commit();
                return $this->redirect(['view', 'id' => $product->id]);
            } catch (Exception $e) {
                $dbTransaction->rollBack();
                echo 'Exception';
            }
        } else {
            return $this->render('create', [
                        'product' => $product,
                        'category' => $category
            ]);
        }
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $product = $this->findModel($id);
        $category = new Category();

        if (Yii::$app->request->post()) {
            $updatedData = Yii::$app->request->post();
            $updatedProduct = [];
            $oldCategories = [];
            $previousPicture = $product->picture;
            $updatedProduct['Product'] = $updatedData['Product'];
            $updatedProduct['_csrf'] = $updatedData['_csrf'];

            if (!($product->CategoryIndexes == $updatedData['Category']['name'])) {
                $manager = new Manager();
                $manager->updateProductCategories($product->CategoryIndexes, $updatedData['Category']['name'], $product->ProductId);
                if (($product->name == $updatedData['Product']['name']) && ($product->description == $updatedData['Product']['description']) 
                        && ($product->price == $updatedData['Product']['price']) && (empty(UploadedFile::getInstance($product, 'file')))) {
                    return $this->redirect(['view', 'id' => $product->id]);
                }
            }

            if (empty(UploadedFile::getInstance($product, 'file'))) {
                $updatedProduct['Product']['picture'] = $previousPicture;

                if ($product->load($updatedProduct)) {
                    $product->save();
                    return $this->redirect(['view', 'id' => $product->id]);
                }
            } else {
                $newFile = UploadedFile::getInstance($product, 'file');
                $ext = pathinfo($newFile, PATHINFO_EXTENSION);
                $randNumber = mt_rand(10, 1000);
                $imageName = strtolower($product->name);
                //save the path in the db column
                $updatedProduct['Product']['picture'] = 'images/products/' . $imageName . $randNumber . '.' . $ext;

                if ($product->load($updatedProduct)) {
                    $product->save();
                    $product->file = $newFile;
                    $product->file->saveAs($updatedProduct['Product']['picture']);

                    //   $product->file->saveAs('images/products/' . $imageName . $randNumber . '.' . $product->file->extension);
                   if($previousPicture != '/images/products/default-product.jpg')
                   {                                        
                    unlink(Yii::$app->basePath.'/web/'.$previousPicture);
                   }
                    return $this->redirect(['view', 'id' => $product->id]);
                }
            }
        } else {
            return $this->render('update', [
                        'product' => $product,
                        'category' => $category
            ]);
        }
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
 {      $deletedProductPicture = $this->findModel($id)->picture;
        CategoryMap::deleteAll(['product_id' => $id]);
        $this->findModel($id)->delete();
        if($deletedProductPicture != '/images/products/default-product.jpg')
        {
        unlink(Yii::$app->basePath . '/web/' . $deletedProductPicture);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
