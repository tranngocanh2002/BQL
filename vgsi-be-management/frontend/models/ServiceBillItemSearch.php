<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceBillItem;

/**
 * ServiceBillItemSearch represents the model behind the search form of `common\models\ServiceBillItem`.
 */
class ServiceBillItemSearch extends ServiceBillItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'service_bill_id', 'service_payment_fee_id', 'service_map_management_id', 'price', 'fee_of_month', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
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
        $query = ServiceBillItem::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'service_bill_id' => $this->service_bill_id,
            'service_payment_fee_id' => $this->service_payment_fee_id,
            'service_map_management_id' => $this->service_map_management_id,
            'price' => $this->price,
            'fee_of_month' => $this->fee_of_month,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
