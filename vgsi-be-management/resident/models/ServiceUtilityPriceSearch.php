<?php

namespace resident\models;

use common\helpers\CUtils;
use common\models\ApartmentMapResidentUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceUtilityPrice;

/**
 * ServiceUtilityPriceSearch represents the model behind the search form of `common\models\ServiceUtilityPrice`.
 */
class ServiceUtilityPriceSearch extends ServiceUtilityPrice
{
    public $apartment_id;
    public $current_time;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
            [['id', 'building_cluster_id', 'service_utility_free_id', 'service_utility_config_id', 'price_hourly', 'price_adult', 'price_child', 'current_time', 'apartment_id'], 'integer'],
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
        $query = ServiceUtilityPriceResponse::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'start_time' => SORT_ASC,
                ]
            ],
        ]);

        $this->load(CUtils::modifyParams($params),'');

        $session = Yii::$app->session;
        $session->set('current_time', $this->current_time);


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
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
            'service_utility_free_id' => $this->service_utility_free_id,
            'service_utility_config_id' => $this->service_utility_config_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'price_hourly' => $this->price_hourly,
            'price_adult' => $this->price_adult,
            'price_child' => $this->price_child,
        ]);

        return $dataProvider;
    }
}
