<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Category;

/**
 * This is the model class for table "Product".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $picture
 * @property string $price
 */
class Product extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public $file;

    public static function tableName() {
        return 'Product';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'description', 'picture', 'price'], 'required'],
            [['description'], 'string'],
            [['price'], 'number', 'min' => 0],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            [['name'], 'string', 'min' => 3, 'max' => 50],
            [['name'], 'trim'],
            [['picture'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'picture' => 'Picture',
            'price' => 'Price',
            'file' => 'Picture',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryMaps() {
        return $this->hasMany(CategoryMap::className(), ['product_id' => 'id'])->with(['category']);
    }

    public function getProductCategories() {
        $categoryList = '';
        $relationData = $this->categoryMaps;
        if (count($relationData) > 1) {
            foreach ($relationData as $key => $object) {
                $categoryList .= $object->category->name . ' ';
            }
            return $categoryList;
        } elseif(count($relationData) == 1) {
            return $relationData[0]->category->name;
        }  else {
            return 'No catagories available';
        }
    }
    
    public function getCategoryIndexes()
    {
        $categoryIndexes = [];
        $relationData = $this->categoryMaps;
         if (count($relationData) > 1) {
            foreach ($relationData as $key => $object) {
                $categoryIndexes[] = $object->category->id;
            }
            return $categoryIndexes;
        } else {
            $categoryIndexes[] = $relationData[0]->category->id;
            return $categoryIndexes;
        }
    }

    public function getPictureInfo() {
        $path = Url::to('@webroot/images/products/');
        $url = Url::to('@web/images/products/');
        $ext = pathinfo($this->picture, PATHINFO_EXTENSION);
        $file = pathinfo($this->picture, PATHINFO_FILENAME);
        $filename = $file . '.' . $ext;
        $alt = $this->name . "'s Profile Picture";
        $imageInfo = ['alt' => $alt];

        if (file_exists($path . $filename)) {
            $imageInfo['url'] = $url . $filename;
        } else {
            $imageInfo['url'] = $url . 'default-product.jpg';
        }
        return $imageInfo;
    }

    public function getExistingCategories() {
        $categories = ArrayHelper::map(Category::find()->all(), 'id', 'name');
        return $categories;
    }
    
    public function getProductId()
    {
       return $this->id;
    }

    public function getDefaulPicture() {
        return '/images/products/default-product.jpg';
    }

}
