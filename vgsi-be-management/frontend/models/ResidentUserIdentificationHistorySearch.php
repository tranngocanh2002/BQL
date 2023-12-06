<?php

namespace frontend\models;

use common\helpers\CUtils;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ResidentUserIdentificationHistory;

/**
 * ResidentUserIdentificationHistorySearch represents the model behind the search form of `common\models\ResidentUserIdentificationHistory`.
 */
class ResidentUserIdentificationHistorySearch extends ResidentUserIdentificationHistory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'resident_user_id', 'type', 'time_event', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['image_name', 'image_uri'], 'safe'],
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
//        $buildingCluster = \Yii::$app->building->BuildingCluster;
        $query = ResidentUserIdentificationHistoryResponse::find();

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
            'resident_user_id' => $this->resident_user_id,
            'type' => $this->type,
            'time_event' => $this->time_event,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'image_name', $this->image_name])
            ->andFilterWhere(['like', 'image_uri', $this->image_uri]);

        return $dataProvider;
    }
}
