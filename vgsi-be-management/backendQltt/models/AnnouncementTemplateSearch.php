<?php

namespace backendQltt\models;

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
            [['id', 'building_cluster_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['content_email', 'content_app', 'content_sms', 'content_pdf'], 'safe'],
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
        $query = AnnouncementTemplate::find()->where(['type' => AnnouncementTemplate::TYPE_POST_NEWS])->orderBy(['created_at' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'building_cluster_id' => $this->building_cluster_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'content_email', $this->content_email])
            ->andFilterWhere(['like', 'content_app', $this->content_app])
            ->andFilterWhere(['like', 'content_sms', $this->content_sms]);

        return $dataProvider;
    }
}
