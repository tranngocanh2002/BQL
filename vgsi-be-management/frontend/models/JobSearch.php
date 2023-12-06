<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\models\Job;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PuriTrakHistory;

/**
 * PuriTrakHistorySearch represents the model behind the search form of `common\models\JobSearch`.
 */
class JobSearch extends Job
{
    public $category;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['time_end', 'time_start', 'status', 'prioritize', 'category', 'title', 'performer', 'people_involved', 'created_by'], 'safe'],
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
        $query = JobResponse::find()->where(['building_cluster_id' => $user->building_cluster_id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ]
            ],
        ]);
        $this->category = -1;
        $this->load(CUtils::modifyParams($params), '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'prioritize' => $this->prioritize,
        ]);
        $currentDateTime = time();
        if(isset($params['status']) && $params['status'] == Job::STATUS_EXPIRE)
        {
            $query->andWhere(['status' => [Job::STATUS_NEW, Job::STATUS_DOING]]);
            $query->andWhere(['<=', 'count_expire', 0]);
            $query->andWhere(['<', 'time_end', $currentDateTime]);
        }
        else if(isset($this->status) && $this->status !== Job::STATUS_EXPIRE){
            $query->andFilterWhere([
                'status' => $this->status,
            ]);
        }else if($this->status === Job::STATUS_EXPIRE){
            $query->andWhere(['status' => [Job::STATUS_NEW, Job::STATUS_DOING]]);
            $query->andWhere(['<=', 'count_expire', 0]);
        }
        //là người tạo, người theo dõi hoặc người thực hiện thì sẽ hiển thị công việc
        $query->andFilterWhere(['or',
            ['created_by' => $user->id],
            ['like', 'performer', ','.$user->id.','],
            ['like', 'people_involved', ','.$user->id.',']
        ]);

        $orLike = ['or'];
        $orAnd = ['and'];
        if(!empty($this->performer) && empty($this->people_involved)){
            $performers = explode(',', trim($this->performer, ','));
            foreach ($performers as $performer){
                array_push($orLike, ['like', 'performer', ','.$performer.',']);
            }
        }
        if(!empty($this->people_involved) && empty($this->performer)){
            $people_involveds = explode(',', trim($this->people_involved, ','));
            foreach ($people_involveds as $people_involved){
                array_push($orLike, ['like', 'people_involved', ','.$people_involved.',']);
            }
        }
        if(!empty($this->performer) && !empty($this->people_involved)){
            $performers = explode(',', trim($this->performer, ','));
            foreach ($performers as $performer){
                array_push($orAnd, ['like', 'performer', ','.$performer.',']);
            }
            $people_involveds = explode(',', trim($this->people_involved, ','));
            foreach ($people_involveds as $people_involved){
                array_push($orAnd, ['like', 'people_involved', ','.$people_involved.',']);
            }
        }
        if($orLike !== ['or']){
            $query->andFilterWhere($orLike);
        }
        if($orAnd !== ['and']){
            $query->andFilterWhere($orAnd);
        }

        if(isset($this->category)){
            if($this->category == Job::CATEGORY_MY_JOB){
                $query->andWhere(['created_by' => $user->id]);
            }else if($this->category == Job::CATEGORY_ASSIGNED_ME){
                $query->andFilterWhere(['like', 'performer', ','.$user->id.',']);
            }else if($this->category == Job::CATEGORY_RELATED_ME){
                $query->andFilterWhere(['like', 'people_involved', ','.$user->id.',']);
            }
        }
        if(!empty($this->created_by)){
            $query->andWhere(['created_by' => $this->created_by]);
        }
        if(!empty($this->time_start)){
            $query->andWhere(['>=', 'time_end', $this->time_start]);
        }
        if(!empty($this->time_end)){
            $query->andWhere(['<=', 'time_end', $this->time_end]);
        }
        $query->andFilterWhere(['like', 'title', $this->title]);
        return $dataProvider;
    }
}
