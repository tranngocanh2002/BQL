<?php

namespace frontend\models;

use common\helpers\CUtils;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ManagementUserNotify;

/**
 * ManagementUserNotifySearch represents the model behind the search form of `common\models\ManagementUserNotify`.
 */
class ManagementUserNotifySearch extends ManagementUserNotify
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'building_cluster_id', 'building_area_id', 'management_user_id', 'type', 'is_read', 'is_hidden', 'request_id', 'request_answer_id', 'request_answer_internal_id', 'service_bill_id', 'created_at', 'updated_at'], 'integer'],
            [['title', 'description'], 'safe'],
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
    public function search($params, $is_web = true)
    {
        $user = Yii::$app->user->getIdentity();
        $query = ManagementUserNotifyResponse::find()
            ->where(['management_user_id' => $user->id, 'building_cluster_id' => $user->building_cluster_id]);

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

        if($is_web !== true){
            $query->andWhere(['or', 
                ['type' => ManagementUserNotify::TYPE_JOB],
                ['type' => ManagementUserNotify::TYPE_FORM],
            ]);
            $query->andWhere([
            'not', ['request_id' => null]
            ]);
        }else{
            $query->andWhere(['<>', 'type', ManagementUserNotify::TYPE_JOB]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'building_cluster_id' => $this->building_cluster_id,
            'building_area_id' => $this->building_area_id,
            'management_user_id' => $this->management_user_id,
            'type' => $this->type,
            'is_read' => $this->is_read,
            'is_hidden' => $this->is_hidden,
            'request_id' => $this->request_id,
            'request_answer_id' => $this->request_answer_id,
            'request_answer_internal_id' => $this->request_answer_internal_id,
            'service_bill_id' => $this->service_bill_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
