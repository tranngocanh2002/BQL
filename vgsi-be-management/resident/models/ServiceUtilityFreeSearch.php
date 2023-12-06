<?php

namespace resident\models;

use common\helpers\CUtils;
use common\models\ApartmentMapResidentUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceUtilityFree;

/**
 * ServiceUtilityFreeSearch represents the model behind the search form of `common\models\ServiceUtilityFree`.
 */
class ServiceUtilityFreeSearch extends ServiceUtilityFree
{
    public $apartment_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
            [['apartment_id', 'id', 'service_id', 'service_map_management_id', 'building_cluster_id', 'building_area_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'code', 'hours_open', 'hours_close', 'description', 'medias'], 'safe'],
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
        $query = ServiceUtilityFreeResponse::find()->andWhere(['status' => [ServiceUtilityFree::STATUS_UNACTIVE, ServiceUtilityFree::STATUS_ACTIVE]]);

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
            $query->where('0=1');
            return $dataProvider;
        }
        if(!empty($this->apartment_id)){
            $apartmentMap = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            $query->where(['building_cluster_id' => $apartmentMap->building_cluster_id]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'service_id' => $this->service_id,
            'service_map_management_id' => $this->service_map_management_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'hours_open', $this->hours_open])
            ->andFilterWhere(['like', 'hours_close', $this->hours_close])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'medias', $this->medias]);

        return $dataProvider;
    }
}
