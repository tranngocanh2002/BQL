<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\models\RequestMapAuthGroup;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Request;

/**
 * RequestSearch represents the model behind the search form of `common\models\Request`.
 */
class RequestSearch extends Request
{
    public $start_time_from;
    public $start_time_to;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'request_category_id', 'resident_user_id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['title', 'title_en', 'content', 'attach', 'start_time_from', 'start_time_to'], 'safe'],
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
        $user = Yii::$app->user->getIdentity();
        $requestMapAuthGroups = RequestMapAuthGroup::find()->where(['auth_group_id' => $user->auth_group_id])->all();
        $requestIds = [];
        foreach ($requestMapAuthGroups as $requestMapAuthGroup){
            $requestIds[] = $requestMapAuthGroup->request_id;
        }
        $query = RequestResponse::find()
            ->with(['requestCategory', 'apartment'])
            ->where(['id' => $requestIds, 'building_cluster_id' => $user->building_cluster_id, 'is_deleted' => Request::NOT_DELETED]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => ['defaultOrder' => [
//                'status' => SORT_ASC,
                'id' => SORT_DESC,
            ]]
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
            'request_category_id' => $this->request_category_id,
            'resident_user_id' => $this->resident_user_id,
            'apartment_id' => $this->apartment_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        if(!empty($this->start_time_from)){
            $query->andWhere(['>=', 'created_at', $this->start_time_from]);
        }

        if(!empty($this->start_time_to)){
            $query->andWhere(['<=', 'created_at', $this->start_time_to]);
        }

        $query->andFilterWhere(['or', ['like', 'title', $this->title], ['like', 'title_en', $this->title]])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'attach', $this->attach]);

        return $dataProvider;
    }
}
