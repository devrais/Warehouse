<?php

namespace app\models;

use app\models\Product;

use Yii;

/**
 * This is the model class for table "Category".
 *
 * @property integer $id
 * @property string $name
 *
 * @property CategoryMap[] $categoryMaps
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'],'required'],
            [['name'], 'string','min'=>3]
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Category Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryMaps()
    {
        return $this->hasMany(CategoryMap::className(), ['category_id' => 'id']);
    }
    
     public function getProducts()
    {
        return $this->hasMany(Product::className(), ['id' => 'product_id'])
                ->viaTable('CategoryMap', ['category_id'=>'id']);
    }
    
   /* public function validateCategoryName($attribute, $params) {
        if (is_array($this->$attribute)) {
            foreach ($this->$attribute as $value) {
                if (!is_string($value)) {
                    $this->addError($attribute, 'List need to contain values type-string');
                }
            }
        } elseif (!is_string($this->$attribute)) {
            $this->addError($attribute, 'Category name is not valid');
        }
    }*/

}
