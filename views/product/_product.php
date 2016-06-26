<?php

use yii\helpers\Html;
use yii\helpers\Url;
/*echo '<pre>';
  print_r($model);
  echo '</pre>';
exit();*/

$photoInfo = $model->PictureInfo;
$photo = Html::img($photoInfo['url'],['alt'=>$photoInfo['alt']]);
$detailLink = Url::toRoute(['/product/view','id'=>$model->id]);
?>

<figure>
    <?=Html::a($photo,$photoInfo['url'],['class'=>'profile-thumb'])?>
</figure>

<ul class="details">
    <li><span>Product Name:</span> <?=$model->name?></li>
    <li><span>Description:</span> <?=$model->description?></li>
    <li><span>Price:</span> <?=$model->price?></li>
    <li><span>Category:</span> <?=$model->ProductCategories?></li>
</ul>

<p><?=Html::a('View complete product description',$detailLink)?></p>
