<?php
namespace resident\models;


use common\helpers\CUtils;
use common\models\AnnouncementCampaign;
use common\models\ApartmentMapResidentUser;
use resident\models\AnnouncementCampaignResponse;
use Yii;
use yii\data\ActiveDataProvider;

class AnnouncementCampaignSearch extends AnnouncementCampaignResponse
{
    public $type;

    public $targets;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['targets'], 'required'],
            // [['is_hidden'], 'integer'],
            // [['title', 'title_en'], 'safe'],
        ];
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
        // Thêm điều kiện IN cho targets
        $aryTargets = [];
        $query = AnnouncementCampaignResponse::find()
            ->where([
                'type' => AnnouncementCampaign::TYPE_POST_NEW,
                'is_send_event' => 0
            ])
            ->andWhere(['<>', 'status', AnnouncementCampaign::STATUS_UNACTIVE])
            ->andWhere(['<', 'send_event_at', time()])
            ->orderBy(['created_at' => SORT_DESC]);
        if (isset($params['targets'])) {
            $query = AnnouncementCampaignResponse::find()
            ->where([
                'type' => AnnouncementCampaign::TYPE_POST_NEW,
                'is_send_event' => 0,
            ])
            ->andWhere(['<>', 'status', AnnouncementCampaign::STATUS_UNACTIVE])
            ->andWhere(['LIKE', 'targets', $params['targets']])
            ->andWhere(['<', 'send_event_at', time()])
            ->orderBy(['created_at' => SORT_DESC]);
        };
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

        // Load dữ liệu từ $params vào model
        $this->load(CUtils::modifyParams($params), '');

        if (!$this->validate()) {
            $query->where('1 != 1');
            return $dataProvider;
        }

        return $dataProvider;
    }

}