<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\models\ManagementUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AnnouncementCampaign;
use yii\helpers\ArrayHelper;

/**
 * AnnouncementCampaignSearch represents the model behind the search form of `common\models\AnnouncementCampaign`.
 */
class AnnouncementCampaignSearch extends AnnouncementCampaign
{
    public $management_user_name;
    public $type_in;
    public $type_not_in;
    public $start_time_from;
    public $start_time_to;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'is_send_push', 'is_send_email', 'is_send_sms', 'send_at', 'building_cluster_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'announcement_category_id', 'is_send'], 'integer'],
            [['title', 'title_en', 'description', 'content', 'attach', 'type_in', 'type_not_in', 'start_time_from', 'start_time_to', 'management_user_name'], 'safe'],
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
        $query = AnnouncementCampaignResponse::find()
            ->where(['building_cluster_id' =>  $user->building_cluster_id])
            ->andWhere(['>=', 'type', AnnouncementCampaign::TYPE_DEFAULT]);

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
//            'is_send_push' => $this->is_send_push,
//            'is_send_email' => $this->is_send_email,
//            'is_send_sms' => $this->is_send_sms,
//            'send_at' => $this->send_at,
//            'building_cluster_id' => $this->building_cluster_id,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
//            'created_by' => $this->created_by,
//            'updated_by' => $this->updated_by,
            'announcement_category_id' => $this->announcement_category_id,
            'is_send' => $this->is_send,
        ]);
        if($this->status !== AnnouncementCampaign::STATUS_PUBLIC_AT){
            $query->andFilterWhere(['status' => $this->status]);
        }else if($this->status === AnnouncementCampaign::STATUS_PUBLIC_AT){
            $query->andWhere(['status' => AnnouncementCampaign::STATUS_ACTIVE]);
            $query->andWhere(
                ['or',
                    ['and', "is_send = " . AnnouncementCampaign::IS_NOT_EVENT, "send_at > " . time()],
                    ['and', "is_send = " . AnnouncementCampaign::IS_EVENT, "send_event_at > " . time()]
                ]
            );
        }
        if(!empty($this->management_user_name)){
            $managementUsers = ManagementUser::find()
                ->where(['building_cluster_id' =>  $user->building_cluster_id, 'is_deleted' => ManagementUser::NOT_DELETED, 'role_type' => ManagementUser::DEFAULT_ADMIN])
                ->andWhere("concat(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) LIKE '%$this->management_user_name%'")
                ->all();
            if(empty($managementUsers)){
                $query->where('0=1');
                return $dataProvider;
            }
            $managementUserIds = ArrayHelper::map($managementUsers, 'id', 'id');
            $query->andWhere(['created_by' => $managementUserIds]);
        }
        if(isset($this->type_in)){
            $query->andWhere(['type' => explode(',', trim($this->type_in, ','))]);
        }

        if(isset($this->type_not_in)){
            $query->andWhere(['not', ['type' => explode(',', trim($this->type_not_in, ','))]]);
        }

        if(!empty($this->start_time_from)){
            $query->andWhere(['>=', 'created_at', $this->start_time_from]);
        }

        if(!empty($this->start_time_to)){
            $query->andWhere(['<=', 'created_at', $this->start_time_to]);
        }

        $query->andFilterWhere(['or', ['like', 'title', $this->title], ['like', 'title_en', $this->title]]);

        return $dataProvider;
    }
}
