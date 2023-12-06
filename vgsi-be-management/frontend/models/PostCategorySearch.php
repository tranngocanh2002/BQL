<?php

namespace frontend\models;

use common\helpers\CUtils;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PostCategory;

/**
 * PostCategorySearch represents the model behind the search form of `common\models\PostCategory`.
 */
class PostCategorySearch extends PostCategory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order', 'id', 'building_cluster_id', 'building_area_id', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
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
        $user = Yii::$app->user->getIdentity();
        $query = PostCategoryResponse::find()->where(['building_cluster_id' => $user->building_cluster_id, 'is_deleted' => PostCategory::NOT_DELETED]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => ['defaultOrder' => [
                'id' => SORT_DESC,
            ]]
        ]);

        $this->load(CUtils::modifyParams($params),'');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'building_cluster_id' => $this->building_cluster_id,
            'building_area_id' => $this->building_area_id,
            'order' => $this->order,
            'is_deleted' => $this->is_deleted,
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
