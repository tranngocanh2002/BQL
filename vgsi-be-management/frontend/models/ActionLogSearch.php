<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\models\ManagementUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ActionLog;

/**
 * ActionLogSearch represents the model behind the search form of `common\models\ActionLog`.
 */
class ActionLogSearch extends ActionLog
{
    public $management_user_name;
    public $start_date;
    public $end_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['_id', 'building_cluster_id', 'management_user_id', 'ip_address', 'user_agent', 'scope', 'headers', 'request', 'response', 'body_params', 'query_params', 'controller', 'action', 'authen', 'created_at', 'start_date', 'end_date', 'management_user_name'], 'safe'],
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
        $a = Yii::$app->params['ConfigActionShowLog'];
        $actions = [];
        foreach ($a as $k => $v){
            $actions = array_merge($actions, array_keys($v['actions']));
        }
        $controller_keys = array_keys($a);
        $query = ActionLogResponse::find()->with(['buildingCluster', 'managementUser'])->where(['building_cluster_id' =>  (int)$buildingCluster->id])->andWhere(['controller' => $controller_keys, 'action' => $actions]);

        // add conditions that should always apply here

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

        $this->load(CUtils::modifyParams($params),'');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['like', '_id', $this->_id])
//            ->andFilterWhere(['like', 'building_cluster_id', $buildingCluster->id])
//            ->andFilterWhere(['like', 'management_user_id', $this->management_user_id])
            ->andFilterWhere(['like', 'ip_address', $this->ip_address])
            ->andFilterWhere(['like', 'user_agent', $this->user_agent])
            ->andFilterWhere(['like', 'scope', $this->scope])
            ->andFilterWhere(['like', 'headers', $this->headers])
            ->andFilterWhere(['like', 'request', $this->request])
            ->andFilterWhere(['like', 'response', $this->response])
            ->andFilterWhere(['like', 'body_params', $this->body_params])
            ->andFilterWhere(['like', 'query_params', $this->query_params])
            ->andFilterWhere(['controller' => $this->controller])
            ->andFilterWhere(['action' => $this->action])
            ->andFilterWhere(['like', 'authen', $this->authen]);
        if(!empty($this->management_user_name)){
            $managementUsers = ManagementUser::find()->where(['or', ['like', 'first_name', $this->management_user_name], ['like', 'last_name', $this->management_user_name]])->all();
            $managementUserIds = [];
            foreach ($managementUsers as $managementUser){
                $managementUserIds[] = $managementUser->id;
            }
            \Yii::info($managementUserIds);
            $query->andWhere(['management_user_id' => $managementUserIds]);
        }
        if(!empty($this->start_date)){
            $query->andWhere(['>=', 'created_at', (int)$this->start_date]);
        }
        if(!empty($this->end_date)){
            $query->andWhere(['<=', 'created_at', (int)$this->end_date]);
        }
        return $dataProvider;
    }
}
