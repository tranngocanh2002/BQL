<?php

namespace backend\models;

use common\models\ManagementUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ManagementUserSearch represents the model behind the search form about `common\models\ManagementUser`.
 */
class ManagementUserSearch extends ManagementUser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'building_cluster_id', 'auth_group_id'], 'integer'],
            [['email', 'phone', 'first_name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = ManagementUser::find()->where(['is_deleted' => ManagementUser::NOT_DELETED]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'building_cluster_id' => $this->building_cluster_id,
            'auth_group_id' => $this->auth_group_id,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'first_name', $this->first_name]);

        return $dataProvider;
    }
}
