<?php

namespace frontend\models;

use common\helpers\CUtils;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceUtilityBooking;

/**
 * ServiceBookingFeeSearch represents the model behind the search form of `common\models\ServiceUtilityBooking`.
 */
class ServiceBookingFeeSearch extends ServiceUtilityBooking
{
    public $is_paid;
    public $start_date;
    public $end_date;
    public $start_time_to;
    public $start_time_from;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'service_utility_config_id', 'service_utility_free_id', 'status', 'start_time', 'end_time', 'total_adult', 'total_child', 'total_slot', 'price', 'fee_of_month', 'service_payment_fee_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'start_date', 'end_date', 'start_time_to', 'start_time_from'], 'integer'],
            [['description', 'json_desc', 'is_paid'], 'safe'],
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
        $this->load(CUtils::modifyParams($params),'');

        $buildingCluster = \Yii::$app->building->BuildingCluster;
        $query = ServiceBookingFeeResponse::find();
        if(isset($this->is_paid)){
            $query->leftJoin('service_payment_fee', 'service_payment_fee.id=service_utility_booking.service_payment_fee_id');
        }
        $query->where(['service_utility_booking.building_cluster_id' => $buildingCluster->id]);
        $query->andWhere([
            'OR',
            ['>', 'service_utility_booking.price', 0],
            ['>', 'service_utility_booking.total_deposit_money', 0],
            ['>', 'service_utility_booking.total_incurred_money', 0],
        ]);

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


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
//            'building_cluster_id' => $this->building_cluster_id,
//            'building_area_id' => $this->building_area_id,
            'service_utility_booking.apartment_id' => $this->apartment_id,
            'service_utility_booking.service_utility_config_id' => $this->service_utility_config_id,
            'service_utility_booking.service_utility_free_id' => $this->service_utility_free_id,
//            'status' => $this->status,
//            'start_time' => $this->start_time,
//            'end_time' => $this->end_time,
//            'total_adult' => $this->total_adult,
//            'total_child' => $this->total_child,
//            'total_slot' => $this->total_slot,
//            'price' => $this->price,
//            'fee_of_month' => $this->fee_of_month,
//            'service_payment_fee_id' => $this->service_payment_fee_id,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
//            'created_by' => $this->created_by,
//            'updated_by' => $this->updated_by,
        ]);

//        $query->andFilterWhere(['like', 'description', $this->description])
//            ->andFilterWhere(['like', 'json_desc', $this->json_desc]);
//        if(!empty($this->start_date)){
//            $query->andWhere(['>=', 'created_at', $this->start_date]);
//        }
//        if(!empty($this->end_date)){
//            $query->andWhere(['<=', 'created_at', $this->end_date]);
//        }
//        if(!empty($this->start_time_from)){
//            $query->andWhere(['>=', 'start_time', $this->start_time_from]);
//        }
//        if(!empty($this->start_time_to)){
//            $query->andWhere(['<=', 'start_time', $this->start_time_to]);
//        }

        if(!empty($this->start_time_from)){
            $query->andWhere(['>=', 'service_utility_booking.updated_at', $this->start_time_from]);
        }
        if(!empty($this->start_time_to)){
            $query->andWhere(['<=', 'service_utility_booking.updated_at', $this->start_time_to]);
        }

        if(isset($this->is_paid)){
//            $query->andWhere(['service_payment_fee.status' => $this->is_paid]);
            $query->andWhere(['service_utility_booking.is_paid' => $this->is_paid]);
        }
        return $dataProvider;
    }
}
