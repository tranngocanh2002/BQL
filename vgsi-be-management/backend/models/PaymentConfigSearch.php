<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PaymentConfig;

/**
 * PaymentConfigSearch represents the model behind the search form of `common\models\PaymentConfig`.
 */
class PaymentConfigSearch extends PaymentConfig
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'building_cluster_id', 'gate', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['receiver_account', 'merchant_id', 'merchant_pass', 'checkout_url_old','checkout_url', 'return_url', 'cancel_url', 'notify_url', 'note'], 'safe'],
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
        $query = PaymentConfig::find();

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
            'building_cluster_id' => $this->building_cluster_id,
            'gate' => $this->gate,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'receiver_account', $this->receiver_account])
            ->andFilterWhere(['like', 'merchant_id', $this->merchant_id])
            ->andFilterWhere(['like', 'merchant_pass', $this->merchant_pass])
            ->andFilterWhere(['like', 'checkout_url', $this->checkout_url])
            ->andFilterWhere(['like', 'checkout_url_old', $this->checkout_url_old])
            ->andFilterWhere(['like', 'return_url', $this->return_url])
            ->andFilterWhere(['like', 'cancel_url', $this->cancel_url])
            ->andFilterWhere(['like', 'notify_url', $this->notify_url])
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
