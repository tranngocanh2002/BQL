<?php

namespace resident\models;

use common\helpers\CUtils;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PuriTrakHistory;

/**
 * PuriTrakHistorySearch represents the model behind the search form of `common\models\PuriTrakHistory`.
 */
class PuriTrakHistorySearch extends PuriTrakHistory
{
    public $start_datetime;
    public $end_datetime;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'puri_trak_id', 'time', 'hours', 'created_at', 'updated_at', 'created_by', 'updated_by', 'start_datetime', 'end_datetime'], 'integer'],
            [['aqi', 'h', 't', 'lat', 'long'], 'number'],
            [['device_id', 'name'], 'safe'],
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
        $query = PuriTrakHistoryResponse::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ]
            ],
        ]);

        $this->load(CUtils::modifyParams($params), '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $this->device_id = PuriTrakHistory::DEVICE_ID;
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'puri_trak_id' => $this->puri_trak_id,
            'aqi' => $this->aqi,
            'h' => $this->h,
            't' => $this->t,
            'time' => $this->time,
            'lat' => $this->lat,
            'long' => $this->long,
            'hours' => $this->hours,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        if(!empty($this->start_datetime)){
            $query->where(['>=', 'hours', $this->start_datetime]);
        }
        if(!empty($this->end_datetime)){
            $query->where(['<=', 'hours', $this->end_datetime]);
        }

        $query->andFilterWhere(['like', 'device_id', $this->device_id])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
