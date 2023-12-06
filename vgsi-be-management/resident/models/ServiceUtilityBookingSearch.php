<?php

namespace resident\models;

use common\helpers\CUtils;
use common\models\ApartmentMapResidentUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceUtilityBooking;

/**
 * ServiceUtilityBookingSearch represents the model behind the search form of `common\models\ServiceUtilityBooking`.
 */
class ServiceUtilityBookingSearch extends ServiceUtilityBooking
{
    public $start_date;
    public $end_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
            [['is_paid', 'id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'service_utility_config_id', 'service_utility_free_id', 'start_time', 'end_time', 'total_adult', 'total_child', 'total_slot', 'price', 'fee_of_month', 'service_payment_fee_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'start_date', 'end_date'], 'integer'],
            [['description', 'json_desc', 'status'], 'safe'],
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
        $query = ServiceUtilityBookingResponse::find();

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
            $query->where('0=1');
            return $dataProvider;
        }
        $arr_status = [];
        if(isset($this->status)){
            $arr_status = explode(',', trim($this->status, ','));
        }
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => $this->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            $query->where('0=1');
            return $dataProvider;
        }
        $query->where(['building_cluster_id' => $apartmentMapResidentUser->building_cluster_id]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'building_cluster_id' => $this->building_cluster_id,
            'building_area_id' => $this->building_area_id,
            'apartment_id' => $this->apartment_id,
            'service_utility_config_id' => $this->service_utility_config_id,
            'service_utility_free_id' => $this->service_utility_free_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'total_adult' => $this->total_adult,
            'total_child' => $this->total_child,
            'total_slot' => $this->total_slot,
            'price' => $this->price,
            'fee_of_month' => $this->fee_of_month,
            'is_paid' => $this->is_paid,
            'service_payment_fee_id' => $this->service_payment_fee_id,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
//            'created_by' => $this->created_by,
//            'updated_by' => $this->updated_by,
        ]);
        if(!empty($arr_status)){
            $query->andWhere(['status' => $arr_status]);
        }
        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'json_desc', $this->json_desc]);
        if(!empty($this->start_date)){
            $query->andWhere(['>=', 'created_at', $this->start_date]);
        }
        if(!empty($this->end_date)){
            $query->andWhere(['<=', 'created_at', $this->end_date]);
        }
        return $dataProvider;
    }
}
