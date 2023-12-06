<?php

namespace backendQltt\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ResidentUser;
use common\models\ApartmentMapResidentUser;
use yii\db\Query;
use yii\db\Expression;

/**
 * ResidentUserSearch represents the model behind the search form of `common\models\ResidentUser`.
 */
class ResidentUserSearch extends ResidentUser
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'gender', 'birthday', 'status', 'status_verify_phone', 'status_verify_email', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'active_app', 'is_send_email', 'is_send_notify', 'ngay_het_han_thi_thuc', 'ngay_dang_ky_tam_chu', 'ngay_dang_ky_nhap_khau', 'ngay_cap_cmtnd', 'deleted_at'], 'integer'],
            [['phone', 'password', 'email', 'first_name', 'last_name', 'avatar', 'auth_key', 'notify_tags', 'cmtnd', 'nationality', 'work', 'so_thi_thuc', 'noi_cap_cmtnd', 'name_search', 'display_name', 'reason'], 'safe'],
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
        $query = ResidentUser::find()->select([
            'first_name',
            'last_name',
            'phone',
            new Expression(':alias AS building_cluster_id', [':alias' => null]),
        ])->where([
            'is_deleted' => ResidentUser::NOT_DELETED,
        ]);

        $queryResidentMapUser = ApartmentMapResidentUser::find()->select([
            'resident_user_first_name AS first_name',
            'resident_user_last_name AS last_name',
            'resident_user_phone AS phone',
            'building_cluster_id',
        ])->where([
            'is_deleted' => ApartmentMapResidentUser::NOT_DELETED,
        ]);

        $unionQuery = (new Query())
        ->from(['unionQuery' => $queryResidentMapUser->union($query)])->groupBy('phone');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $unionQuery,
        ]);
        
        $this->load($params);

        $unionQuery->andFilterWhere(['like', 'first_name', trim($this->first_name)])
        ->andFilterWhere(['like', 'last_name', trim($this->last_name)])
        ->andFilterWhere(['like', 'phone', trim($this->phone)]);

        return $dataProvider;
    }
}
