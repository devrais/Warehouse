<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Product;

/**
 * ProductSearch represents the model behind the search form about `app\models\Product`.
 */
class ProductSearch extends Product
{
    public $beginPrice;
    public $endPrice;
    public $category;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'description', 'picture'], 'safe'],
            [['price', 'beginPrice', 'endPrice'], 'number'],
            [['category'],'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find();

        // add conditions that should always apply here
        
         $query->joinWith(['categoryMaps.category']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'name',
                    'description',
                    'picture',
                    'price'
                ]
            ],
           /* 'pagination' =>[
                'pageSize' => 3
            ]*/
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'Category.name' => $this->category
        ]);

        $query->andFilterWhere(['like', 'Product.name', $this->name])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'picture', $this->picture])
                ->andFilterWhere(['>=', 'price', $this->beginPrice])
                ->andFilterWhere(['<=', 'price', $this->endPrice]);

        return $dataProvider;
    }
}
