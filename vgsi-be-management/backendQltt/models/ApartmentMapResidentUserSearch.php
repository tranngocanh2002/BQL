<?php

namespace backendQltt\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUser;
use yii\db\Query;
use yii\helpers\VarDumper;
use yii\db\Expression;
use common\helpers\CUtils;

/**
 * ApartmentMapResidentUserSearch represents the model behind the search form of `common\models\ApartmentMapResidentUser`.
 */
class ApartmentMapResidentUserSearch extends ApartmentMapResidentUser
{
    public $total_apartment;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'apartment_id', 'resident_user_id', 'building_cluster_id', 'building_area_id', 'type', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'apartment_capacity', 'resident_user_gender', 'resident_user_birthday', 'install_app', 'resident_user_is_send_email', 'type_relationship', 'resident_user_is_send_notify', 'ngay_cap_cmtnd', 'ngay_dang_ky_nhap_khau', 'ngay_dang_ky_tam_chu', 'ngay_het_han_thi_thuc', 'last_active', 'is_deleted', 'deleted_at', 'total_apartment'], 'integer'],
            [['apartment_name', 'apartment_code', 'resident_user_phone', 'resident_user_email', 'resident_user_first_name', 'resident_user_last_name', 'resident_user_avatar', 'apartment_parent_path', 'apartment_short_name', 'resident_user_nationality', 'resident_name_search', 'cmtnd', 'noi_cap_cmtnd', 'work', 'so_thi_thuc'], 'safe'],
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
        $query = ResidentUser::find()->select([
            'first_name as resident_user_first_name',
            'last_name as resident_user_last_name',
            'phone as resident_user_phone',
            new Expression(':alias AS building_cluster_id', [':alias' => null]),
            new Expression('(select count(id) from apartment_map_resident_user where `is_deleted`=0 and resident_user_phone = resident_user.phone) as ac'),
        ])->where([
            'is_deleted' => ResidentUser::NOT_DELETED,
        ]);

        $queryResidentMapUser = ApartmentMapResidentUser::find()->select([
            'resident_user_first_name',
            'resident_user_last_name',
            'resident_user_phone',
            'building_cluster_id',
            new Expression('1 as ru'),
        ])->where([
            'is_deleted' => ApartmentMapResidentUser::NOT_DELETED,
            'install_app'=> ApartmentMapResidentUser::INSTALL_APP
        ]);

        $unionQuery = (new Query())
        ->from(['unionQuery' => $queryResidentMapUser->union($query)])->groupBy('resident_user_phone');

        $dataProvider = new ActiveDataProvider([
            'query' => $unionQuery,
        ]);
        
        $this->load($params);

        if (isset($this->total_apartment) && trim($this->total_apartment) != '') {
            $totalApartments = ApartmentMapResidentUser::find()
            ->select([
                'count(*) as total_apartment', 
                'resident_user_phone',
                'building_cluster_id',
                new Expression('1 as ru'),
            ])
            ->where([
                'is_deleted' => ApartmentMapResidentUser::NOT_DELETED,
            ])
            ->groupBy('resident_user_phone')
            ->having("total_apartment = {$this->total_apartment}")
            ->asArray()
            ->all();
            
            $phones = [];
            foreach ($totalApartments as $totalApartment) {
                if (isset($totalApartment['resident_user_phone']) && $totalApartment['resident_user_phone'] != '') $phones[] = $totalApartment['resident_user_phone'];
            }
            
            $unionQuery->where(['in', 'resident_user_phone', $phones]);
        }

        if (in_array($this->type, ['0', '1'])) {
            $apartmentMapResidentUsers = ApartmentMapResidentUser::find()
            ->select('resident_user_phone')
            ->where([
                'is_deleted' => ApartmentMapResidentUser::NOT_DELETED,
            ])
            ->groupBy('resident_user_phone')
            ->asArray()
            ->all();

            $residentUsers = ResidentUser::find()
            ->select('phone')
            ->where([
                'is_deleted' => ResidentUser::NOT_DELETED,
            ])
            ->groupBy('phone')
            ->asArray()
            ->all();

            $phoneApartmentMapResidentUsers = $phoneResidentUsers = [];
            foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser) {
                if (isset($apartmentMapResidentUser['resident_user_phone']) && $apartmentMapResidentUser['resident_user_phone'] != '') $phoneApartmentMapResidentUsers[] = $apartmentMapResidentUser['resident_user_phone'];
            }

            foreach ($residentUsers as $residentUser) {
                if (isset($residentUser['phone']) && $residentUser['phone'] != '') $phoneResidentUsers[] = $residentUser['phone'];
            }

            if ($this->type == '1') {
                $phones = array_values(array_intersect($phoneApartmentMapResidentUsers, $phoneResidentUsers));
            } else {
                $phones = array_diff($phoneResidentUsers, $phoneApartmentMapResidentUsers);
            }
            $unionQuery->where(['in', 'resident_user_phone', $phones]);
        }

        $unionQuery->andFilterWhere([
            'building_cluster_id' => $this->building_cluster_id,
        ]);

        $unionQuery->andFilterWhere(['like', 'resident_user_first_name', trim($this->resident_user_first_name)])
        ->andFilterWhere(['like', 'resident_user_last_name', trim($this->resident_user_last_name)])
        ->andFilterWhere(['or',['like', 'resident_user_phone', trim($this->resident_user_phone)], ['like', 'resident_user_phone', preg_replace('/^0/', '84', trim($this->resident_user_phone))]]);

        // if (trim($this->resident_user_phone) || trim($this->resident_user_phone) != '') {
        //     $phones = [
        //         CUtils::validateMsisdn($this->resident_user_phone, 1),
        //         CUtils::validateMsisdn($this->resident_user_phone),
        //     ];
        //     $unionQuery->where(['in', 'resident_user_phone', $phones]);
        // }

        return $dataProvider;
    }
}
