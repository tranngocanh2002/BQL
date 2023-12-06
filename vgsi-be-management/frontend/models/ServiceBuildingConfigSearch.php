<?php

namespace frontend\models;

use common\helpers\CUtils;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceBuildingConfig;

/**
 * ServiceBuildingConfigSearch represents the model behind the search form of `common\models\ServiceBuildingConfig`.
 */
class ServiceBuildingConfigSearch extends ServiceBuildingConfig
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'service_id', 'service_map_management_id', 'building_cluster_id', 'building_area_id', 'price', 'unit', 'day', 'month_cycle', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['cr_minutes', 'cr_hours', 'cr_days', 'cr_months', 'cr_days_of_week'], 'safe'],
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
        $query = ServiceBuildingConfigResponse::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
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
            'service_id' => $this->service_id,
            'service_map_management_id' => $this->service_map_management_id,
            'building_cluster_id' => $this->building_cluster_id,
            'building_area_id' => $this->building_area_id,
            'price' => $this->price,
            'unit' => $this->unit,
            'day' => $this->day,
            'month_cycle' => $this->month_cycle,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'cr_minutes', $this->cr_minutes])
            ->andFilterWhere(['like', 'cr_hours', $this->cr_hours])
            ->andFilterWhere(['like', 'cr_days', $this->cr_days])
            ->andFilterWhere(['like', 'cr_months', $this->cr_months])
            ->andFilterWhere(['like', 'cr_days_of_week', $this->cr_days_of_week]);

        return $dataProvider;
    }
}
