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
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update', 'delete', 'create'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'create'],
                        'roles' => ['worker', 'manager' , 'owner']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['manager', 'owner']
                    ]
                ],
                'denyCallback' => function($rule, $action) {
            if ($action->id == 'delete') {
                throw new ForbiddenHttpException('Only managers and owners can delete products.');
            } elseif ($action->id == 'update') {
                throw new ForbiddenHttpException('Only workers, managers and owners can update products.');
            } elseif ($action->id == 'create') {
                throw new ForbiddenHttpException('Only workers, managers and owners can create products.');
            } else {
                if (Yii::$app->user->isGuest) {
                    Yii::$app->user->loginRequired();
                }
            }
        }
            ]
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
       $postData = Yii::$app->request->post();
       $newId=''; 
       $manager = new Manager();
       
        if (isset($postData['_csrf']) && isset($postData['Product']) && isset($postData['Category'])) {
            $newProduct['_csrf'] = $postData['_csrf'];
            $newProduct['Product'] = $postData['Product'];
            $newMap['_csrf'] = $postData['_csrf'];
            $categories = $postData['Category']['name'];
            
            $newId = $manager->createProductWithCategory($newProduct, $newMap, $categories);
            if ($newId) {
                return $this->redirect(['view', 'id' =>$newId ]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Failed to create Product !!!');
                return $this->redirect(['create']);
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
        $newRelaionId = '';
        $newProductId = '';

        if (Yii::$app->request->post()) {
            $updatedData = Yii::$app->request->post();
            $updatedProduct = [];
            $previousPicture = $product->picture;
            $updatedProduct['Product'] = $updatedData['Product'];
            $updatedProduct['_csrf'] = $updatedData['_csrf'];    
            $manager = new Manager();

            if (!($product->CategoryIndexes == $updatedData['Category']['name'])) {
                $newRelaionId = $manager->updateProductCategories($product->CategoryIndexes, $updatedData['Category']['name'], $product->ProductId);
                if (($newRelaionId) && (($product->name == $updatedData['Product']['name']) && ($product->description == $updatedData['Product']['description']) 
                        && ($product->price == $updatedData['Product']['price']) && (empty(UploadedFile::getInstance($product, 'file'))))) {
                    return $this->redirect(['view', 'id' => $newRelaionId]);
                } else {
                    $newProductId = $manager->updateProduct($updatedProduct, $product);
                    if ($newProductId) {
                        return $this->redirect(['view', 'id' => $newProductId]);
                    } else {
                        Yii::$app->getSession()->setFlash('error', 'Failed to update Product !!!');
                        return $this->redirect(['update']);
                    }
                }
            } else {
                $newProductId = $manager->updateProduct($updatedProduct, $product);
                if ($newProductId) {
                    return $this->redirect(['view', 'id' => $newProductId]);
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Failed to update Product !!!');
                    return $this->redirect(['update']);
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
