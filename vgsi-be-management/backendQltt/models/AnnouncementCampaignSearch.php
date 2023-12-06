<?php

namespace backendQltt\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AnnouncementCampaign;

/**
 * AnnouncementCampaignSearch represents the model behind the search form of `common\models\AnnouncementCampaign`.
 */
class AnnouncementCampaignSearch extends AnnouncementCampaign
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'is_send_push', 'is_send_email', 'is_send_sms', 'send_at', 'building_cluster_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'announcement_category_id', 'is_send', 'total_apartment_send', 'total_apartment_open', 'is_event', 'is_send_event', 'send_event_at', 'type', 'total_email_send', 'total_email_open', 'total_sms_send', 'total_apartment_send_success', 'total_email_send_success', 'total_sms_send_success', 'total_app_send', 'total_app_open', 'total_app_success', 'is_survey', 'survey_deadline', 'type_report'], 'integer'],
            [['title', 'description', 'content', 'attach', 'cr_minutes', 'cr_hours', 'cr_days', 'cr_months', 'cr_days_of_week', 'content_sms', 'apartment_not_send_ids', 'add_phone_send', 'add_email_send', 'title_en', 'apartment_ids', 'targets'], 'safe'],
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
        $query = AnnouncementCampaign::find()->where(['type' => AnnouncementCampaign::TYPE_POST_NEW])->orderBy(['created_at' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'sort' => false,
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'is_send_push' => $this->is_send_push,
            'is_send_email' => $this->is_send_email,
            'is_send_sms' => $this->is_send_sms,
            'send_at' => $this->send_at,
            'building_cluster_id' => $this->building_cluster_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'announcement_category_id' => $this->announcement_category_id,
            'is_send' => $this->is_send,
            'total_apartment_send' => $this->total_apartment_send,
            'total_apartment_open' => $this->total_apartment_open,
            'is_event' => $this->is_event,
            'is_send_event' => $this->is_send_event,
            'send_event_at' => $this->send_event_at,
            'type' => $this->type,
            'total_email_send' => $this->total_email_send,
            'total_email_open' => $this->total_email_open,
            'total_sms_send' => $this->total_sms_send,
            'total_apartment_send_success' => $this->total_apartment_send_success,
            'total_email_send_success' => $this->total_email_send_success,
            'total_sms_send_success' => $this->total_sms_send_success,
            'total_app_send' => $this->total_app_send,
            'total_app_open' => $this->total_app_open,
            'total_app_success' => $this->total_app_success,
            'is_survey' => $this->is_survey,
            'survey_deadline' => $this->survey_deadline,
            'type_report' => $this->type_report,
        ]);

        $query->andFilterWhere(['like', 'title', trim($this->title)])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'attach', $this->attach])
            ->andFilterWhere(['like', 'cr_minutes', $this->cr_minutes])
            ->andFilterWhere(['like', 'cr_hours', $this->cr_hours])
            ->andFilterWhere(['like', 'cr_days', $this->cr_days])
            ->andFilterWhere(['like', 'cr_months', $this->cr_months])
            ->andFilterWhere(['like', 'cr_days_of_week', $this->cr_days_of_week])
            ->andFilterWhere(['like', 'content_sms', $this->content_sms])
            ->andFilterWhere(['like', 'apartment_not_send_ids', $this->apartment_not_send_ids])
            ->andFilterWhere(['like', 'add_phone_send', $this->add_phone_send])
            ->andFilterWhere(['like', 'add_email_send', $this->add_email_send])
            ->andFilterWhere(['like', 'title_en', $this->title_en])
            ->andFilterWhere(['like', 'apartment_ids', $this->apartment_ids])
            ->andFilterWhere(['like', 'targets', $this->targets]);

        return $dataProvider;
    }
}
