<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\models\ApartmentMapResidentUser;
use common\models\HistoryResidentMapApartment;
use common\models\ResidentUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ResidentUserSearch represents the model behind the search form of `frontend\models\ResidentUser`.
 */
class ApartmentMapResidentUserSearchByPhone extends ApartmentMapResidentUser
{
    public $name;
    public $phone;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['phone', 'name'], 'safe'],
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
        $query = ApartmentMapResidentUserResponseByPhone::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['or',
            ['like', 'resident_user_phone', $this->phone],
            ['like', 'resident_user_phone', preg_replace('/^0/', '84', $this->phone)]
        ]);
        $query->groupBy('resident_user_phone');
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchOld($params)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $historyMaps = HistoryResidentMapApartment::find()->where(['building_cluster_id' => $buildingCluster->id])->groupBy(['resident_user_id'])->all();
        $ids = [];
        foreach ($historyMaps as $historyMap){
            $ids[] = $historyMap->resident_user_id;
        }
        $query = ResidentUserOldResponse::find()
            ->andWhere(['is_deleted' => ResidentUser::NOT_DELETED]);

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $ids,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'first_name', $this->name])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name]);

        return $dataProvider;
    }
}
