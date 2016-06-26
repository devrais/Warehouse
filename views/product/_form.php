<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Category;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">
 
     <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo "<div class='alert alert-danger'>" . $message . "</div>";
    }
    ?>
    
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    
     <?= $form->field($category, 'name')->dropDownList($product->ExistingCategories, ['multiple' => 'true']) ?>
    
    <?= $form->field($product, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($product, 'description')->textarea(['rows' => 6]) ?>

         <label class="checkbox-inline">       
            <input type="checkbox" value="" id="radio-update-picture" onchange="enableFileInput();">Insert picture 
        </label>
 <?//= Html::checkbox($product->isNewRecord ? 'Add Picture', ['id'='radio-update-picture', 'onchange'='enableFileInput()'] : 'Update Picture', ['id'='radio-update-picture', 'onchange'='enableFileInput()']) ?>
    
    <?= $form->field($product, 'file')->fileInput(['disabled'=>'disabled']) ?>

    <?= $form->field($product, 'price')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($product->isNewRecord ? 'Create' : 'Update', ['class' => $product->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
     

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJsFile('@web/js/product.js') ?>



