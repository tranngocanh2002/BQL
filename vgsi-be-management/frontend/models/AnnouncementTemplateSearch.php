<?php

namespace frontend\models;

use common\helpers\CUtils;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AnnouncementTemplate;

/**
 * AnnouncementTemplateSearch represents the model behind the search form of `common\models\AnnouncementTemplate`.
 */
class AnnouncementTemplateSearch extends AnnouncementTemplate
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'building_cluster_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'type'], 'integer'],
            [['name', 'name_en', 'content_email', 'content_app', 'content_sms'], 'safe'],
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
        $query = AnnouncementTemplateResponse::find()->where(['or', ['building_cluster_id' => null], ['building_cluster_id' => $buildingCluster->id]]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'building_cluster_id' => SORT_ASC,
                    'id' => SORT_ASC,
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
            'building_cluster_id' => $this->building_cluster_id,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'content_email', $this->content_email])
            ->andFilterWhere(['like', 'content_app', $this->content_app])
            ->andFilterWhere(['or', ['like', 'name', $this->name], ['like', 'name_en', $this->name]])
            ->andFilterWhere(['like', 'content_sms', $this->content_sms]);

        return $dataProvider;
    }
}
