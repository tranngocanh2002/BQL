<?php

namespace console\models;

use common\helpers\CUtils;
use Yii;
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
            [['id', 'status', 'is_send_push', 'is_send_email', 'is_send_sms', 'send_at', 'building_cluster_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'announcement_category_id', 'is_send'], 'integer'],
            [['title', 'description', 'content', 'attach'], 'safe'],
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
        $minute_current = (int)date('i');
        $hour_current = (int)date('H');
        $day_current = (int)date('d');
        $month_current = (int)date('m');
        $day_of_week_current = (int)date('w');
        $query = AnnouncementCampaign::find()
            ->where(['or',
                ['is_send_push' => AnnouncementCampaign::IS_SEND_PUSH],
                ['is_send_email' => AnnouncementCampaign::IS_SEND_EMAIL]
            ])
            ->andWhere(['status' => AnnouncementCampaign::STATUS_ACTIVE])
            ->andWhere(['or',
                ['like', 'cr_minutes', ',' . $minute_current . ','],
                ['cr_minutes' => '*']
            ])
            ->andWhere(['or',
                ['like', 'cr_hours', ',' . $hour_current . ','],
                ['cr_hours' => '*']
            ])
            ->andWhere(['or',
                ['like', 'cr_days', ',' . $day_current . ','],
                ['cr_days' => '*']
            ])
            ->andWhere(['or',
                ['like', 'cr_months', ',' . $month_current . ','],
                ['cr_months' => '*']
            ])
            ->andWhere(['or',
                ['like', 'cr_days_of_week', ',' . $day_of_week_current . ','],
                ['cr_days_of_week' => '*']
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => isset($params['page']) && $params['page'] > 0 ? (int)$params['page'] - 1 : 0,
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load(CUtils::modifyParams($params), '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'announcement_category_id' => $this->announcement_category_id,
            'is_send' => $this->is_send,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
