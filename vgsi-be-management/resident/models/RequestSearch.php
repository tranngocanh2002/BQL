<?php

namespace resident\models;

use common\helpers\CUtils;
use common\models\ApartmentMapResidentUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Request;

/**
 * RequestSearch represents the model behind the search form of `common\models\Request`.
 */
class RequestSearch extends Request
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'request_category_id', 'resident_user_id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['request_category_id', 'title', 'title_en', 'content', 'attach'], 'safe'],
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
        $query = RequestResponse::find()->joinWith(['residentUser', 'requestCategory', 'apartment'])->where(['request.is_deleted' => Request::NOT_DELETED]);

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
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }
        //check map căn hộ
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'request.id' => $this->id,
            'request.request_category_id' => $this->request_category_id,
            'request.status' => $this->status,
            'request.apartment_id' => $this->apartment_id,
            'request.created_at' => $this->created_at,
            'request.updated_at' => $this->updated_at,
            'request.created_by' => $this->created_by,
            'request.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['or', ['like', 'request.title', $this->title], ['like', 'request.title_en', $this->title]])
            ->andFilterWhere(['like', 'request.content', $this->content])
            ->andFilterWhere(['like', 'request.attach', $this->attach]);

        return $dataProvider;
    }
}
