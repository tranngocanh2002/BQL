<?php

namespace frontend\models;

use common\helpers\ApiHelper;
use common\helpers\CUtils;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceUtilityForm;
use frontend\models\ServiceUtilityFormResponse;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * RequestSearch represents the model behind the search form of `common\models\ServiceUtilityForm`.
 */
class ServiceUtilityFormSearch extends ServiceUtilityForm
{
    public $apartment_name;
    public $resident_user_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'resident_user_id', 'apartment_id', 'type'], 'integer'],
            [['title', 'apartment_name', 'resident_user_name'], 'safe'],
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
        $query = ServiceUtilityFormResponse::find()->where(['building_cluster_id' => $user->building_cluster_id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => ['defaultOrder' => [
//                'status' => SORT_ASC,
                'id' => SORT_DESC,
            ]]
        ]);

        $this->load(CUtils::modifyParams($params),'');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $apartment_ids = [];
        if(!empty($this->apartment_name)){
            $apartments = Apartment::find()->where(['like', 'name', $this->apartment_name])->all();
            if(empty($apartments)){
                $query->where('0=1');
                return $dataProvider;
            }
            $apartment_ids = ArrayHelper::map($apartments, 'id', 'id');
        }
        $resident_user_ids = [];
        if(!empty($this->resident_user_name)){
            $residentUsers = ApartmentMapResidentUser::find()
                ->where(['OR',
                    ['like', 'resident_user_first_name', $this->resident_user_name],
                    ['like', 'resident_name_search', $this->resident_user_name]
                ])
                ->all();
            if(empty($residentUsers)){
                $query->where('0=1');
                return $dataProvider;
            }
            foreach ($residentUsers as $residentUser){
                if(isset($residentUser->resident)){
                    $resident_user_ids[] = $residentUser->resident->id;
                }
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'resident_user_id' => $resident_user_ids,
            'apartment_id' => $apartment_ids,
            'status' => $this->status,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
