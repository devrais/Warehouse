<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "Product".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $picture
 * @property string $price
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    
    public $file;
            
    public static function tableName()
    {
        return 'Product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'picture', 'price'], 'required'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['file'], 'file'],
            [['name'], 'string', 'max' => 50],
            [['picture'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'picture' => 'Picture',
            'price' => 'Price',
            'file' => 'Picture'
        ];
    }
    
    public function getPictureInfo()
    {
        $path = Url::to('@webroot/images/products/');
        $url = Url::to('@web/images/products/');
        $ext = pathinfo($this->picture, PATHINFO_EXTENSION);
        $file = pathinfo($this->picture, PATHINFO_FILENAME);
        $filename = $file.'.'. $ext;
        $alt = $this->name . "'s Profile Picture";

        $imageInfo = ['alt'=> $alt];

        if (file_exists($path . $filename)) {
            $imageInfo['url'] =  $url.$filename;
        } else {
            $imageInfo['url'] =  $url.'default-product.jpg';
        }

        return $imageInfo;
    }
}
