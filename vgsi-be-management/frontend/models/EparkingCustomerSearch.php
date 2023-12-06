<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ApartmentMapResidentUser;

/**
 * ApartmentMapResidentUserSearch represents the model behind the search form of `common\models\ApartmentMapResidentUser`.
 */
class EparkingCustomerSearch extends ApartmentMapResidentUser
{
    public $name;
    public $phone;
    public $email;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'apartment_id', 'resident_user_id', 'building_cluster_id', 'building_area_id', 'type', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'apartment_capacity', 'resident_user_gender', 'resident_user_birthday'], 'integer'],
            [['apartment_name', 'apartment_short_name', 'name', 'phone', 'email'], 'safe'],
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
        $query = EparkingCustomerResponse::find();

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
        if(empty($this->building_cluster_id)){
            $this->building_cluster_id = 1;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'apartment_id' => $this->apartment_id,
            'resident_user_id' => $this->resident_user_id,
            'building_cluster_id' => $this->building_cluster_id,
//            'building_area_id' => $this->building_area_id,
            'type' => $this->type,
//            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
//            'apartment_capacity' => $this->apartment_capacity,
//            'resident_user_gender' => $this->resident_user_gender,
//            'resident_user_birthday' => $this->resident_user_birthday,
        ]);
        $query->andFilterWhere(['or',['like', 'resident_user_phone', $this->phone], ['like', 'resident_user_phone', preg_replace('/^0/', '84', $this->phone)]])
            ->andFilterWhere(['like', 'resident_name_search', CVietnameseTools::removeSigns2($this->name)])
            ->andFilterWhere(['like', 'apartment_name', $this->apartment_name])
            ->andFilterWhere(['like', 'apartment_short_name', $this->apartment_short_name]);
        $query->groupBy('resident_user_id');

        return $dataProvider;
    }
}
