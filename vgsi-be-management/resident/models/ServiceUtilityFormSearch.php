<?php

namespace resident\models;

use common\helpers\CUtils;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceUtilityForm;
use resident\models\ServiceUtilityFormResponse;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Request;

/**
 * RequestSearch represents the model behind the search form of `common\models\ServiceUtilityForm`.
 */
class ServiceUtilityFormSearch extends ServiceUtilityForm
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
            [['id', 'status', 'apartment_id'], 'integer'],
            [['title'], 'safe'],
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
        $query = ServiceUtilityFormResponse::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
//                    'status' => SORT_ASC,
//                    'updated_at' => SORT_DESC,
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load(CUtils::modifyParams($params),'');
//        check apartment map user
        $checkApartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => $this->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($checkApartmentMapResidentUser)){
            $query->where('0=1');
            return $dataProvider;
        }
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'status' => $this->status,
            'apartment_id' => $this->apartment_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
