<?php
/**
 * Created by PhpStorm.
 * User: qhuy.duong@gmail.com
 * Date: 15/05/2019
 * Time: 6:18 CH
 */

namespace frontend\models;


use common\helpers\CUtils;
use common\models\AnnouncementSurvey;
use frontend\models\AnnouncementSurveyResponse;
use Yii;
use yii\data\ActiveDataProvider;

class AnnouncementSurveySearch extends AnnouncementSurvey
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id', 'resident_user_id', 'status', 'announcement_campaign_id'], 'integer'],
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
        $query = AnnouncementSurveyResponse::find()->where(['building_cluster_id' =>  $user->building_cluster_id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);
        $this->load(CUtils::modifyParams($params),'');
        if (!$this->validate()) {
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'resident_user_id' => $this->resident_user_id,
            'apartment_id' => $this->apartment_id,
            'announcement_campaign_id' => $this->announcement_campaign_id
        ]);
        return $dataProvider;
    }
}
