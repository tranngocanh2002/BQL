<?php

namespace frontend\models;

use common\helpers\CUtils;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceProvider;

/**
 * ServiceProviderSearch represents the model behind the search form of `common\models\ServiceProvider`.
 */
class ServiceProviderSearch extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'is_deleted', 'building_cluster_id', 'building_area_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'using_bank_cluster'], 'integer'],
            [['name', 'address', 'description', 'medias'], 'safe'],
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
        $query = ServiceProviderResponse::find()->where(['building_cluster_id' => $user->building_cluster_id, 'is_deleted' => ServiceProvider::NOT_DELETED]);

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
            'status' => $this->status,
            'using_bank_cluster' => $this->using_bank_cluster,
            'building_cluster_id' => $this->building_cluster_id,
            'building_area_id' => $this->building_area_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
