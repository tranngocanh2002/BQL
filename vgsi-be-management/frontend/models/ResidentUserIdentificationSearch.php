<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ResidentUserIdentification;

/**
 * ResidentUserIdentificationSearch represents the model behind the search form of `common\models\ResidentUserIdentification`.
 */
class ResidentUserIdentificationSearch extends ResidentUserIdentification
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'resident_user_id', 'building_cluster_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['medias'], 'safe'],
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
        $query = ResidentUserIdentificationResponse::find();

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
        //chỉ trả về những resident chưa được xác thực
        $this->status = ResidentUserIdentification::STATUS_INACTIVE;

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'resident_user_id' => $this->resident_user_id,
            'building_cluster_id' => $this->building_cluster_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'medias', $this->medias]);

        return $dataProvider;
    }
}
