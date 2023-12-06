<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\models\JobComment;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PuriTrakHistorySearch represents the model behind the search form of `common\models\JobCommentSearch`.
 */
class JobCommentSearch extends JobComment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['job_id', 'type'], 'integer'],
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
        $query = JobCommentResponse::find()->where(['building_cluster_id' => $user->building_cluster_id]);
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
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'job_id' => $this->job_id,
            'type' => $this->type,
        ]);
        return $dataProvider;
    }
}
