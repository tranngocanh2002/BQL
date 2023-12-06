<?php
namespace resident\models;


use common\helpers\CUtils;
use common\models\AnnouncementItem;
use common\models\ApartmentMapResidentUser;
use resident\models\AnnouncementItemResponse;
use Yii;
use yii\data\ActiveDataProvider;

class AnnouncementItemSearch extends AnnouncementItem
{
    public $title;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
            [['is_hidden'], 'integer'],
            [['title', 'title_en'], 'safe'],
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
        $query = AnnouncementItemResponse::find()->where(['phone'=>$user->phone])->joinWith('announcementCampaign');

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
            $query->where('1 != 1');
            return $dataProvider;
        }
        if(empty($this->apartment_id)){
            $query->where('1 != 1');
            return $dataProvider;
        }
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => (int)$this->apartment_id, 'resident_user_phone' => $user->phone, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            $query->where('1 != 1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'apartment_id' => $this->apartment_id,
            'is_hidden' => $this->is_hidden,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $query->andWhere('announcement_campaign_id in (select id from announcement_campaign where is_survey = 0) or announcement_campaign_id in (select id from announcement_campaign where is_survey = 1 and id in (select announcement_campaign_id from announcement_survey where resident_user_id = '.$user->id.' group by announcement_campaign_id))');
        $query->andFilterWhere(['or', ['like', 'announcement_campaign.title', $this->title], ['like', 'announcement_campaign.title', $this->title_en]])
            ->andFilterWhere(['like', 'announcement_campaign.title_en', $this->title_en]);

        return $dataProvider;
    }
}