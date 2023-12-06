<?php

namespace resident\models;

use common\helpers\CUtils;
use common\models\ApartmentMapResidentUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EparkingCardHistory;

/**
 * EparkingCardHistorySearch represents the model behind the search form of `common\models\EparkingCardHistory`.
 */
class EparkingCardHistorySearch extends EparkingCardHistory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'vehicle_type', 'card_type', 'ticket_type', 'datetime_in', 'datetime_out', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'service_management_vehicle_id', 'apartment_id'], 'integer'],
            [['serial', 'plate_in', 'image1_in', 'image2_in', 'plate_out', 'image1_out', 'image2_out'], 'safe'],
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
        $query = EparkingCardHistoryResponse::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ]
            ],
        ]);

        $this->load(CUtils::modifyParams($params),'');

        if(empty($this->apartment_id)){
            $query->where('0=1');
            return $dataProvider;
        }else{
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($apartmentMapResidentUser)){
                $query->where('0=1');
                return $dataProvider;
            }
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'apartment_id' => $this->apartment_id,
            'vehicle_type' => $this->vehicle_type,
            'card_type' => $this->card_type,
            'ticket_type' => $this->ticket_type,
            'datetime_in' => $this->datetime_in,
            'datetime_out' => $this->datetime_out,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'service_management_vehicle_id' => $this->service_management_vehicle_id,
        ]);

        $query->andFilterWhere(['like', 'serial', $this->serial])
            ->andFilterWhere(['like', 'plate_in', $this->plate_in])
            ->andFilterWhere(['like', 'image1_in', $this->image1_in])
            ->andFilterWhere(['like', 'image2_in', $this->image2_in])
            ->andFilterWhere(['like', 'plate_out', $this->plate_out])
            ->andFilterWhere(['like', 'image1_out', $this->image1_out])
            ->andFilterWhere(['like', 'image2_out', $this->image2_out]);

        return $dataProvider;
    }
}
