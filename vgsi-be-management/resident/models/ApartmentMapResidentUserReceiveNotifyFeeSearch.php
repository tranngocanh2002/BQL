<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ApartmentMapResidentUserReceiveNotifyFee;

/**
 * ApartmentMapResidentUserReceiveNotifyFeeSearch represents the model behind the search form of `common\models\ApartmentMapResidentUserReceiveNotifyFee`.
 */
class ApartmentMapResidentUserReceiveNotifyFeeSearch extends ApartmentMapResidentUserReceiveNotifyFee
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
            [['id', 'building_cluster_id', 'apartment_id', 'resident_user_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['phone', 'email'], 'safe'],
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
        //check map apartment
        $user = \Yii::$app->user->identity;
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => $this->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $query = ApartmentMapResidentUserReceiveNotifyFee::find()->where(['apartment_id' => $this->apartment_id]);

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
        $query->andFilterWhere([
            'id' => $this->id,
            'building_cluster_id' => $this->building_cluster_id,
            'apartment_id' => $this->apartment_id,
            'resident_user_id' => $this->resident_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
