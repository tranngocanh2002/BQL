<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\models\Contractor;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PuriTrakHistorySearch represents the model behind the search form of `common\models\ContractorSearch`.
 */
class ContractorSearch extends Contractor
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'contact_phone', 'status'], 'safe'],
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
        $user = \Yii::$app->user->getIdentity();
        $query = ContractorResponse::find()->where(['building_cluster_id' => $user->building_cluster_id, 'is_deleted' => Contractor::NOT_DELETED]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);
        $this->load(CUtils::modifyParams($params), '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);
        if(isset($this->status)){
            $query->andFilterWhere([
                'status' => $this->status,
            ]);
        }
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['or',
            ['like', 'contact_phone', $this->contact_phone],
            ['like', 'contact_phone', preg_replace('/^0/', '84', $this->contact_phone)]
        ]);
        return $dataProvider;
    }
}
