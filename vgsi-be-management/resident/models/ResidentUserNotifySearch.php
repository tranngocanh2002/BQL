<?php

namespace resident\models;

use common\helpers\CUtils;
use common\models\ApartmentMapResidentUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ResidentUserNotify;

/**
 * ResidentUserNotifySearch represents the model behind the search form of `common\models\ResidentUserNotify`.
 */
class ResidentUserNotifySearch extends ResidentUserNotify
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'building_cluster_id', 'building_area_id', 'resident_user_id', 'type', 'is_read', 'is_hidden', 'request_id', 'request_answer_id', 'request_answer_internal_id', 'service_bill_id', 'announcement_item_id', 'created_at', 'updated_at', 'apartment_id','service_utility_form_id'], 'integer'],
            [['title', 'description'], 'safe'],
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
        $apartmentMap = ApartmentMapResidentUser::find()->where(['resident_user_phone' => $user->phone])->all();
        $buildingClusterIds = [];
        foreach ($apartmentMap as $item){
            $buildingClusterIds[] = $item->building_cluster_id;
        }
        $query = ResidentUserNotifyResponse::find()->where(['resident_user_id' => $user->id, 'building_cluster_id' => $buildingClusterIds]);
        if("-9999" == Yii::$app->request->get('apartment_id'))
        {
            $query = ResidentUserNotifyResponse::find()->where(['resident_user_id' => $user->id]);
        }
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
        if("-9999" != Yii::$app->request->get('apartment_id'))
        {
            $query->andFilterWhere([
                'id' => $this->id,
                'building_cluster_id' => $this->building_cluster_id,
                'building_area_id' => $this->building_area_id,
                'resident_user_id' => $this->resident_user_id,
                'apartment_id' => [$this->apartment_id,-1],
                'type' => $this->type,
                'is_read' => $this->is_read,
                'is_hidden' => $this->is_hidden,
                'request_id' => $this->request_id,
                'request_answer_id' => $this->request_answer_id,
                'request_answer_internal_id' => $this->request_answer_internal_id,
                'service_bill_id' => $this->service_bill_id,
                'announcement_item_id' => $this->announcement_item_id,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]);
            $query->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'description', $this->description]);
    
        }
        return $dataProvider;    
    }
}
