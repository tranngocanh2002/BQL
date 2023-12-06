<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use Yii;
use common\helpers\CUtils;
use common\models\Apartment;
use common\models\ServiceMapManagement;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServicePaymentFee;

/**
 * ServicePaymentFeeSearch represents the model behind the search form of `common\models\ServicePaymentFee`.
 */
class ServicePaymentFeeSearch extends ServicePaymentFee
{
    public $from_month;
    public $to_month;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
            [['id', 'service_map_management_id', 'building_cluster_id', 'apartment_id', 'price', 'status', 'fee_of_month', 'day_expired', 'created_at', 'updated_at', 'created_by', 'updated_by', 'building_area_id', 'from_month', 'to_month'], 'integer'],
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
        $user = Yii::$app->user->getIdentity();
        $query = ServicePaymentFeeResponse::find()->with('serviceMapManagement');

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
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => $this->apartment_id, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            $query->where('0=1');
            return $dataProvider;
        }
        $query->where(['building_cluster_id' => $apartmentMapResidentUser->building_cluster_id, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT, 'status' => ServicePaymentFee::STATUS_UNPAID]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'service_map_management_id' => $this->service_map_management_id,
            'apartment_id' => $this->apartment_id,
            'price' => $this->price,
            'day_expired' => $this->day_expired,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'building_area_id' => $this->building_area_id,
        ]);
        if(!empty($this->from_month) && !empty($this->to_month)){
            $query->andWhere(['and', "fee_of_month >= $this->from_month", "fee_of_month <= $this->to_month"]);
        }
        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
