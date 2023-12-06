<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\models\BuildingArea;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BuildingAreaSearch represents the model behind the search form of `frontend\models\BuildingArea`.
 */
class BuildingAreaSearch extends BuildingArea
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'type'], 'integer'],
            [['name', 'description', 'short_name'], 'safe'],
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
        $query = BuildingAreaResponse::find()->where(['building_cluster_id' => $user->building_cluster_id, 'is_deleted' => BuildingArea::NOT_DELETED]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ]
            ],
        ]);

        $this->load(CUtils::modifyParams($params),'');

        if (!$this->validate()) {
            Yii::error($this->errors);
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

//         grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'parent_id' => $this->parent_id,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $this->name = trim(trim($this->name, '/'));
        $lo = $tang = trim($this->name);
        $is_search_lo = false;
        if(!empty($this->name)){
            if (strpos($this->name, '/') !== false) {
                $is_search_lo = true;
                list($lo, $tang) = explode('/', $this->name);
            }
            $lo = trim(trim($lo,'/'));
            $tang = trim(trim($tang,'/'));
        }
        Yii::warning($lo);
        Yii::warning($tang);
        if($is_search_lo == false){
            $query->andFilterWhere(['or', ['like', 'name', $tang], ['like', 'parent_path', $lo]]);
        }else{
            $query->andFilterWhere(['like', 'name', $tang]);
            $query->andFilterWhere(['like', 'parent_path', $lo]);
        }
        $query->andFilterWhere(['like', 'short_name', $this->short_name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
