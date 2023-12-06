<?php

namespace frontend\models;

use common\helpers\CUtils;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PaymentGenCode;

/**
 * PaymentGenCodeSearch represents the model behind the search form of `common\models\PaymentGenCode`.
 */
class PaymentGenCodeSearch extends PaymentGenCode
{
    public $start_date;
    public $end_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'building_cluster_id', 'apartment_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'type', 'is_auto', 'payment_order_id', 'lock_time', 'resident_user_id', 'start_date', 'end_date'], 'integer'],
            [['service_payment_fee_ids', 'code'], 'safe'],
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
        $query = PaymentGenCodeResponse::find()->where(['building_cluster_id' => $buildingCluster->id]);

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
            'apartment_id' => $this->apartment_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'type' => $this->type,
            'is_auto' => $this->is_auto,
            'payment_order_id' => $this->payment_order_id,
            'lock_time' => $this->lock_time,
            'resident_user_id' => $this->resident_user_id,
        ]);

        if(!empty($this->start_date)){
            $query->where(['>=', 'created_at', $this->start_date]);
        }
        if(!empty($this->end_date)){
            $query->where(['<=', 'created_at', $this->end_date]);
        }
        $query->andFilterWhere(['like', 'service_payment_fee_ids', $this->service_payment_fee_ids])
            ->andFilterWhere(['like', 'code', $this->code]);

        return $dataProvider;
    }
}
