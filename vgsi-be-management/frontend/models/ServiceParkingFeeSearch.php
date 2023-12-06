<?php

namespace frontend\models;

use common\helpers\CUtils;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceParkingFee;

/**
 * ServiceParkingFeeSearch represents the model behind the search form of `common\models\ServiceParkingFee`.
 */
class ServiceParkingFeeSearch extends ServiceParkingFee
{
    public $from_month;
    public $to_month;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fee_of_month', 'id', 'service_map_management_id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'count_month', 'start_time', 'end_time', 'service_parking_level_id', 'total_money', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'service_management_vehicle_id', 'from_month', 'to_month'], 'integer'],
            [['description'], 'safe'],
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
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $query = ServiceParkingFeeResponse::find()->with(['apartment', 'serviceMapManagement', 'servicePaymentFee'])->where(['building_cluster_id' => $buildingCluster->id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC,
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
            'service_map_management_id' => $this->service_map_management_id,
            'building_cluster_id' => $this->building_cluster_id,
            'building_area_id' => $this->building_area_id,
            'apartment_id' => $this->apartment_id,
            'count_month' => $this->count_month,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'service_parking_level_id' => $this->service_parking_level_id,
            'total_money' => $this->total_money,
            'status' => $this->status,
            'fee_of_month' => $this->fee_of_month,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'service_management_vehicle_id' => $this->service_management_vehicle_id,
        ]);
        if(!empty($this->from_month) && !empty($this->to_month)){
            $query->andWhere(['and', "fee_of_month >= $this->from_month", "fee_of_month <= $this->to_month"]);
        }
        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
