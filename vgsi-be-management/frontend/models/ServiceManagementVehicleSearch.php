<?php

namespace frontend\models;

use common\helpers\CUtils;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceManagementVehicle;

/**
 * ServiceManagementVehicleSearch represents the model behind the search form of `common\models\ServiceManagementVehicle`.
 */
class ServiceManagementVehicleSearch extends ServiceManagementVehicle
{
    public $from_date;
    public $to_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'service_parking_level_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'from_date', 'to_date', 'status', 'end_date'], 'integer'],
            [['number', 'description'], 'safe'],
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
        $buildingCluster = \Yii::$app->building->BuildingCluster;
        $query = ServiceManagementVehicleResponse::find()->where(['building_cluster_id' => $buildingCluster->id, 'is_deleted' => ServiceManagementVehicle::NOT_DELETED]);

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
            'building_cluster_id' => $this->building_cluster_id,
            'building_area_id' => $this->building_area_id,
            'apartment_id' => $this->apartment_id,
            'service_parking_level_id' => $this->service_parking_level_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        if(!empty($this->from_date) && !empty($this->to_date)){
            $query->andWhere(['and', "start_date >= $this->from_date", "end_date <= $this->to_date"]);
        }
        if(!empty($this->end_date) && !empty($this->end_date)){
            $query->andWhere(['>=', 'end_date', $this->end_date]);
        }
        $query->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
