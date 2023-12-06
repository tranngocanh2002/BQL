<?php

namespace resident\models;

use common\helpers\CUtils;
use common\models\Apartment;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceBill;

/**
 * ServiceBillSearch represents the model behind the search form of `common\models\ServiceBill`.
 */
class ServiceBillSearch extends ServiceBill
{
    public $created_at_from;
    public $created_at_to;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
            [['id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'management_user_id', 'resident_user_id', 'type_payment', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'created_at_from', 'created_at_to', 'type'], 'integer'],
            [['number', 'management_user_name'], 'string'],
            [['code', 'resident_user_name', 'payer_name'], 'safe'],
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
        $query = ServiceBillResponse::find()->with(['apartment', 'managementUser']);

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

        $arr_status = [ServiceBill::STATUS_PAID, ServiceBill::STATUS_UNPAID, ServiceBill::STATUS_BLOCK];
        if(isset($this->status)){
            $arr_status = [$this->status];
            if($this->status == ServiceBill::STATUS_PAID){
                $arr_status = [ServiceBill::STATUS_PAID, ServiceBill::STATUS_BLOCK];
            }
        }

        $apartment = Apartment::findOne(['id' => $this->apartment_id]);
        $query->where(['building_cluster_id' => $apartment->building_cluster_id, 'status' => $arr_status]);
        if(!empty($this->created_at_from) && !empty($this->created_at_to)){
            $query->andFilterWhere(['between', 'created_at', $this->created_at_from, $this->created_at_to]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'building_cluster_id' => $this->building_cluster_id,
            'building_area_id' => $this->building_area_id,
            'apartment_id' => $this->apartment_id,
            'management_user_id' => $this->management_user_id,
            'resident_user_id' => $this->resident_user_id,
            'type_payment' => $this->type_payment,
            'is_deleted' => $this->is_deleted,
            'type' => $this->type,
//            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'management_user_name', $this->management_user_name])
            ->andFilterWhere(['like', 'resident_user_name', $this->resident_user_name])
            ->andFilterWhere(['like', 'payer_name', $this->payer_name]);

        return $dataProvider;
    }
}
