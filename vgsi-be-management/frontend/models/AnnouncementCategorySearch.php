<?php
/**
 * Created by PhpStorm.
 * User: qhuy.duong@gmail.com
 * Date: 15/05/2019
 * Time: 6:18 CH
 */

namespace frontend\models;


use common\helpers\CUtils;
use common\models\AnnouncementCategory;
use frontend\models\AnnouncementCategoryResponse;
use Yii;
use yii\data\ActiveDataProvider;

class AnnouncementCategorySearch extends AnnouncementCategory
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'name_en'], 'safe'],
            [['type'], 'integer'],
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
        $query = AnnouncementCategoryResponse::find()->where(['building_cluster_id' =>  $user->building_cluster_id]);

        // add conditions that should always apply here

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
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['or', ['like', 'name', $this->name], ['like', 'name_en', $this->name]]);

        return $dataProvider;
    }
}