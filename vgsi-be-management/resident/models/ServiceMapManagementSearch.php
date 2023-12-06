<?php

namespace resident\models;

use common\helpers\CUtils;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceMapManagement;

/**
 * ServiceMapManagementSearch represents the model behind the search form of `common\models\ServiceMapManagement`.
 */
class ServiceMapManagementSearch extends ServiceMapManagement
{
    public $apartment_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
            [['id', 'service_id', 'service_provider_id', 'status', 'is_deleted', 'building_cluster_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'service_type'], 'integer'],
            [['service_name', 'service_name_en', 'service_description', 'medias'], 'safe'],
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
        $query = ServiceMapManagementResponse::find()->where(['is_deleted' => ServiceMapManagement::NOT_DELETED]);

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
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => $this->apartment_id, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            $query->where('0=1');
            return $dataProvider;
        }
        $query->where(['building_cluster_id' => $apartmentMapResidentUser->building_cluster_id]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'service_id' => $this->service_id,
            'service_provider_id' => $this->service_provider_id,
            'status' => $this->status,
            'is_deleted' => $this->is_deleted,
            'building_cluster_id' => $this->building_cluster_id,
            'building_area_id' => $this->building_area_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'service_type' => $this->service_type,
        ]);

        $query->andFilterWhere(['or', ['like', 'service_name', $this->service_name], ['like', 'service_name_en', $this->service_name]])
            ->andFilterWhere(['like', 'service_description', $this->service_description])
            ->andFilterWhere(['like', 'medias', $this->medias]);

        return $dataProvider;
    }
}
