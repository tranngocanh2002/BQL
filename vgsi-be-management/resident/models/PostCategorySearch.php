<?php

namespace resident\models;

use common\helpers\CUtils;
use common\models\Apartment;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PostCategory;

/**
 * PostCategorySearch represents the model behind the search form of `common\models\PostCategory`.
 */
class PostCategorySearch extends PostCategory
{
    public $apartment_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
            [['id', 'building_cluster_id', 'building_area_id', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'name_en', 'color'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = PostCategoryResponse::find()->where(['is_deleted' => PostCategory::NOT_DELETED]);;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load(CUtils::modifyParams($params),'');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }
        $apartment = Apartment::findOne(['id' => $this->apartment_id]);
        $query->andWhere(['building_cluster_id' => $apartment->building_cluster_id]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['or', ['like', 'name', $this->name], ['like', 'name_en', $this->name]])
            ->andFilterWhere(['like', 'color', $this->color]);

        return $dataProvider;
    }
}
