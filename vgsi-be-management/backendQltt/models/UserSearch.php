<?php

namespace backendQltt\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\models\UserRole;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at','role_id'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'email','phone', 'full_name','code_user'], 'safe'],
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
        $query = User::find()->where(['NOT', ['id' => [1]]])->orderBy(['created_at' => SORT_DESC]);

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
            'status' => $this->status,
            'role_id' => $this->role_id,
            'created_at' => $this->created_at,
            // 'full_name' => trim($this->full_name),
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'role_id', $this->role_id])
            ->andFilterWhere(['like', 'full_name', trim($this->full_name)])
            ->andFilterWhere(['like', 'email', trim($this->email)])
            ->andFilterWhere(['like', 'code_user', trim($this->code_user)]);

        return $dataProvider;
    }
}
