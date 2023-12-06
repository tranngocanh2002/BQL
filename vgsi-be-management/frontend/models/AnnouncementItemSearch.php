<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\models\AnnouncementCampaign;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUser;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AnnouncementItem;
use Yii;

/**
 * AnnouncementItemSearch represents the model behind the search form of `common\models\AnnouncementItem`.
 */
class AnnouncementItemSearch extends AnnouncementItem
{
    public $building_area_ids;
    public $targets;
    public $text_search;
    public $type_send;
    public $start_time;
    public $end_time;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'announcement_campaign_id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'status', 'created_at', 'updated_at', 'type_send', 'start_time', 'end_time'], 'integer'],
            [['building_area_ids', 'text_search', 'targets'], 'safe']
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
        $query = AnnouncementItemResponse::find()->where(['building_cluster_id' =>  $buildingCluster->id]);

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
            $dataCountRes = [
                'total_apartment' => 0,
                'total_email' => 0,
                'total_app' => 0,
                'total_sms' => 0,
            ];
            return [
                'dataCount' => $dataCountRes,
                'dataProvider' => $dataProvider
            ];
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'announcement_campaign_id' => $this->announcement_campaign_id,
        ]);

        $announcementCampaign = AnnouncementCampaign::findOne(['id' => $this->announcement_campaign_id]);
        //lấy danh sách gửi theo apartmentId
        // $aparmentId = json_decode($announcementCampaign->apartment_ids,true) ?? "";
        // if(!empty($aparmentId))
        // {
        //     $query->andFilterWhere(['in', 'apartment_id', $aparmentId]);
        //     $query->andFilterWhere(['>','end_debt',0]);
        // }
        $dataCountRes = [
            'total_apartment' => $announcementCampaign->total_apartment_send,
            'total_email' => $announcementCampaign->total_email_send,
            'total_app' => $announcementCampaign->total_app_send,
            'total_sms' => $announcementCampaign->total_sms_send,
        ];
        return [
            'dataCount' => $dataCountRes,
            'dataProvider' => $dataProvider
        ];
    }

    public function searchSend($params)
    {
        $buildingCluster = \Yii::$app->building->BuildingCluster;
        $query = AnnouncementSendResponse::find()->where(['building_cluster_id' => $buildingCluster->id, 'is_deleted' => Apartment::NOT_DELETED]);

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
            return [
                'dataCount' => 0,
                'dataProvider' => $dataProvider
            ];
        }
        $building_area_ids = [];
        if(!empty($this->building_area_ids)){
            $building_area_ids = explode(',', $this->building_area_ids);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'building_area_id' => $building_area_ids,
        ]);

        $dataCountRes = AnnouncementCampaign::countTotalSend($buildingCluster->id, $building_area_ids);
        return [
            'dataCount' => $dataCountRes,
            'dataProvider' => $dataProvider
        ];
    }
    public function searchSendNew($params)
    {
        $buildingCluster = \Yii::$app->building->BuildingCluster;
        $query = AnnouncementSendNewResponse::find()->where(['building_cluster_id' => $buildingCluster->id, 'is_deleted' => Apartment::NOT_DELETED]);

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
            return [
                'dataCount' => 0,
                'dataProvider' => $dataProvider
            ];
        }
        $building_area_ids = [];
        if(!empty($this->building_area_ids)){
            $building_area_ids = explode(',', $this->building_area_ids);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'building_area_id' => $building_area_ids,
        ]);

        $targets = [];
        if(!empty($this->targets)){
            $targets = explode(',', $this->targets);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'type' => $targets
        ]);

        $dataCountRes = AnnouncementCampaign::countTotalSendNew($buildingCluster->id, $building_area_ids, $targets);
        return [
            'dataCount' => $dataCountRes,
            'dataProvider' => $dataProvider
        ];
    }


    public function searchListSend($params)
    {
        $buildingCluster = \Yii::$app->building->BuildingCluster;
        $query = AnnouncementItemTotalSendResponse::find()->where(['building_cluster_id' =>  $buildingCluster->id]);
        $queryCount = AnnouncementItem::find()->where(['building_cluster_id' =>  $buildingCluster->id]);

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

        if(empty($this->start_time)){
            $this->start_time = strtotime(date('Y-m-01 00:00:00', time()));
        }
        if(empty($this->end_time)){
            $this->end_time = strtotime(date('Y-m-t 23:59:59', time()));
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            $dataCountRes = [
                'total_send' => 0,
                'total_limit' => 0,
            ];
            return [
                'dataCount' => $dataCountRes,
                'dataProvider' => $dataProvider
            ];
        }

        $query->andWhere(['>=', 'created_at', $this->start_time])->andWhere(['<=', 'created_at', $this->end_time]);
        $queryCount->andWhere(['>=', 'created_at', $this->start_time])->andWhere(['<=', 'created_at', $this->end_time]);
        $total_limit = 0;
        if($this->type_send == 1){
            $query->andWhere(['<>', 'email', ''])->andWhere(['not', ['email' => null]]);
            $queryCount->andWhere(['<>', 'email', ''])->andWhere(['not', ['email' => null]]);
            $total_limit = $buildingCluster->limit_email;
            \Yii::info($buildingCluster->limit_email);
        }else if($this->type_send == 2){
            $query->andWhere(['<>', 'phone', ''])->andWhere(['not', ['phone' => null]]);
            $queryCount->andWhere(['<>', 'phone', ''])->andWhere(['not', ['phone' => null]]);
            $total_limit = $buildingCluster->limit_sms;
            \Yii::info($buildingCluster->limit_sms);
        }else if($this->type_send == 3){
            $query->andWhere(['<>', 'device_token', ''])->andWhere(['not', ['device_token' => null]]);
            $queryCount->andWhere(['<>', 'device_token', ''])->andWhere(['not', ['device_token' => null]]);
            $total_limit = $buildingCluster->limit_notify;
            \Yii::info($buildingCluster->limit_notify);
        }

        if(!empty($this->text_search)){
            $query->andWhere(['or',
                ['like', 'phone', $this->text_search],
                ['like', 'email', $this->text_search],
                ['like', 'device_token', $this->text_search]
            ]);
            $queryCount->andWhere(['or',
                ['like', 'phone', $this->text_search],
                ['like', 'email', $this->text_search],
                ['like', 'device_token', $this->text_search]
            ]);
        }

        $total_send = $queryCount->count();
        $dataCountRes = [
            'total_send' => (int)$total_send,
            'total_limit' => $total_limit,
        ];
        return [
            'dataCount' => $dataCountRes,
            'dataProvider' => $dataProvider
        ];
    }

    public function searchByIdAnnouncementCampaign($params)
    {
        $params['page']     = 1 ;
        $params['pageSize'] = 200000 ;
        $buildingCluster = \Yii::$app->building->BuildingCluster;
        $query = AnnouncementItemResponse::find()->where(['building_cluster_id' =>  $buildingCluster->id]);

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
            $dataCountRes = [
                'total_apartment' => 0,
                'total_email' => 0,
                'total_app' => 0,
                'total_sms' => 0,
            ];
            return [
                'dataCount' => $dataCountRes,
                'dataProvider' => $dataProvider
            ];
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'announcement_campaign_id' => $this->announcement_campaign_id,
        ]);

        $announcementCampaign = AnnouncementCampaign::findOne(['id' => $this->announcement_campaign_id]);
        $dataCountRes = [
            'total_apartment' => $announcementCampaign->total_apartment_send,
            'total_email' => $announcementCampaign->total_email_send,
            'total_app' => $announcementCampaign->total_app_send,
            'total_sms' => $announcementCampaign->total_sms_send,
        ];
        return [
            'dataCount' => $dataCountRes,
            'dataProvider' => $dataProvider
        ];
    }

    public function searchByIdAparmentMapResidentUser($apartmentIds = [],$targets = [])
    {
        // $buildingCluster = \Yii::$app->building->BuildingCluster;
        $data = ApartmentMapResidentUser::find()
        ->where([
            'apartment_id' => $apartmentIds,
            'is_deleted' => ApartmentMapResidentUser::NOT_DELETED
        ])
        ->andWhere(['in', 'type', $targets])
        ->all();
        return $data;
    }
}
