<?php

use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */

$this->title = 'Warehouse';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1> Welcome to "Warehouse"</h1>

        <p><a <?= Html::a('See Products', '/basic/web/product', ['class'=>'btn btn-lrg btn-success'])?></p>
        <p><a <?= Html::a('See Categories', '/basic/web/category', ['class'=>'btn btn-lrg btn-info'])?></p>
        <p><a <?= Html::a('See Employees list', '/basic/web/employee', ['class'=>'btn btn-lrg btn-primary'])?></p>
    </div>

   

    </div>
</div>
