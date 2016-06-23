<?php

namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\Category;
use app\models\CategoryMap;
use app\models\ProductSearch;
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
        
     /*   $ext = pathinfo('images/products/Computer.png', PATHINFO_EXTENSION);
        print_r($ext);
        exit();*/
        
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
      /*   echo '<pre>';
  print_r(Yii::$app->request->queryParams);
  echo '</pre>';
    exit();*/

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
      
        if (Yii::$app->request->post()) {
            $dbTransaction = Yii::$app->db->beginTransaction();
            $postData = Yii::$app->request->post();

            if (isset($postData['_csrf']) && isset($postData['Product']) && isset($postData['Category'])) {

                $newProduct['_csrf'] = $postData['_csrf'];
                $newProduct['Product'] = $postData['Product'];
                $newCategory['_csrf'] = $postData['_csrf'];
                $newCategory['Category'] = $postData['Category'];      
                $newMap['_csrf'] = $postData['_csrf'];

                try {
                    $product->load($newProduct);

                    //get the instance of the uploaded file
                    $imageName = strtolower($product->name);
                    $product->file = UploadedFile::getInstance($product, 'file');
                    $randNumber = mt_rand(10, 1000);
                    //save the path in the db column
                    $product->picture = 'images/products/' . $imageName . $randNumber . '.' . $product->file->extension;
                    $product->save();

                   // $newProductId = Yii::$app->db->getLastInsertID();
                    $newMap['CategoryMap']['product_id'] = Yii::$app->db->getLastInsertID();
                    
                    foreach ($newCategory['Category']['name'] as $categoryId) {
                        $newMap['CategoryMap']['category_id'] = $categoryId;                  
                        $map->load($newMap);                   
                        $map->save();             
                        $map = new CategoryMap();                 
                    }
                   
                    $product->file->saveAs('images/products/' . $imageName . $randNumber . '.' . $product->file->extension);
                    $dbTransaction->commit();
                    return $this->redirect(['view', 'id' => $product->id]);
 
                } catch (Exception $e) {
                    $dbTransaction->rollBack();
                    echo 'Exception';    
                }
            }else
            {
                echo 'Post data from form is corrupted !!!';
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
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $previousPicture = $pictureUrl = Yii::$app->basePath.'/web/'. $model->picture;

        if ($model->load(Yii::$app->request->post())) {
             //get the instance of the uploaded file
            $imageName = strtolower($model->name);
            $model->file = UploadedFile::getInstance($model, 'file');

            $randNumber = mt_rand(10, 1000);

            //save the path in the db column
            $model->picture = 'images/products/' . $imageName . $randNumber . '.' . $model->file->extension;
            $model->save();
            $model->file->saveAs('images/products/' . $imageName . $randNumber . '.' . $model->file->extension);
            unlink($previousPicture);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
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
    {      
        $pictureUrl = Yii::$app->basePath.'/web/'. $this->findModel($id)->picture;
        $this->findModel($id)->delete();
        unlink($pictureUrl);
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
