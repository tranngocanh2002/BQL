<?php

namespace backendQltt\models;

use common\models\rbac\AuthGroup;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AuthGroupSearch represents the model behind the search form about `common\models\AuthGroup`.
 */
class AuthGroupSearch extends AuthGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'building_cluster_id'], 'integer'],
            [['name', 'description'], 'safe'],
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
        $query = AuthGroup::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'sort' => false,
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
            'building_cluster_id' => $this->building_cluster_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
