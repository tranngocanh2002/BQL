<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentMapResidentUserResponse")
 * )
 */
class ApartmentMapResidentUserResponse extends ApartmentMapResidentUser
{
    /**
     * @SWG\Property(property="apartment_map_resident_user_id", type="integer", description="id bảng map"),
     * @SWG\Property(property="id", type="integer", description="id resident user"),
     * @SWG\Property(property="phone", type="string"),
     * @SWG\Property(property="email", type="string"),
     * @SWG\Property(property="first_name", type="string"),
     * @SWG\Property(property="last_name", type="string"),
     * @SWG\Property(property="avatar", type="string"),
     * @SWG\Property(property="gender", type="integer"),
     * @SWG\Property(property="birthday", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer", description="Id căn hộ"),
     * @SWG\Property(property="apartment_name", type="string", description="Tên căn hộ"),
     * @SWG\Property(property="apartment_code", type="string", description="Mã căn hộ"),
     * @SWG\Property(property="apartment_capacity", type="string", description="Diện tích căn hộ"),
     * @SWG\Property(property="apartment_parent_path", type="string", description="Thông tin cụm và building area"),
     * @SWG\Property(property="type", type="integer", description="0 - Gia đình chủ hộ, 1 - chủ hộ, 2 - khách thuê, 3 - Gia đình khách thuê"),
     * @SWG\Property(property="type_relationship", type="integer", description="Quan hệ với chủ hộ: 0 - Chủ hộ, 1 - Ông/Bà, 2 - Bố/Mẹ, 3 - Vợ/Chồng, 4 - Con, 5 - Anh/chị/em, 6 - Bạn, 7 - khác"),
     * @SWG\Property(property="type_relationship_name", type="string"),
     * @SWG\Property(property="type_relationship_name_en", type="string"),
     * @SWG\Property(property="cmtnd", type="string"),
     * @SWG\Property(property="is_check_cmtnd", type="integer"),
     * @SWG\Property(property="ngay_cap_cmtnd", type="integer"),
     * @SWG\Property(property="noi_cap_cmtnd", type="string"),
     * @SWG\Property(property="nationality", type="string"),
     * @SWG\Property(property="work", type="string"),
     * @SWG\Property(property="so_thi_thuc", type="string"),
     * @SWG\Property(property="ngay_het_han_thi_thuc", type="integer"),
     * @SWG\Property(property="ngay_dang_ky_tam_chu", type="integer"),
     * @SWG\Property(property="ngay_dang_ky_nhap_khau", type="integer"),
     * @SWG\Property(property="install_app", type="integer", description="0 - chưa cài app, 1 - đã cài app"),
     * @SWG\Property(property="total_apartment", type="integer", description="số lượng bds"),
     * @SWG\Property(property="history_resident_map_apartments", description="Lịch sử vào ra căn hộ", type="array", @SWG\Items(type="object", ref="#/definitions/HistoryResidentMapApartmentResponse"),),
     * @SWG\Property(property="created_at", type="integer", description="ngày vào"),
     * @SWG\Property(property="deleted_at", type="integer", description="ngày ra"),
     */

    public function fields()
    {
        return [
            'apartment_map_resident_user_id' => function ($model) {
                return $model->id;
            },
            'id' => function ($model) {
                return $model->resident_user_id;
            },
            'phone' => function ($model) {
                return $model->resident_user_phone;
            },
            'email' => function ($model) {
                return $model->resident_user_email;
            },
            'first_name' => function ($model) {
                return $model->resident_user_first_name;
            },
            'last_name' => function ($model) {
                return $model->resident_user_last_name;
            },
            'avatar' => function ($model) {
                return $model->resident_user_avatar;
            },
            'gender' => function($model){
                if(empty($model->resident_user_gender)){
                    return ResidentUser::GENDER_1;
                }
                return $model->resident_user_gender;
            },
            'birthday' => function ($model) {
                return $model->resident_user_birthday;
            },
            'type',
            'type_relationship',
            'type_relationship_name' => function($model){
                return ApartmentMapResidentUser::$type_relationship_list[$model->type_relationship] ?? null;
            },
            'type_relationship_name_en' => function($model){
                return ApartmentMapResidentUser::$type_relationship_en_list[$model->type_relationship] ?? null;
            },
            'apartment_id',
            'apartment_name',
            'apartment_code',
            'apartment_capacity',
            'apartment_parent_path' => function($model){
                return trim($model->apartment_parent_path, '/');
            },
            'cmtnd',
            'ngay_cap_cmtnd',
            'noi_cap_cmtnd',
            'nationality' => function($model){
                return $model->resident_user_nationality;
            },
            'work',
            'so_thi_thuc',
            'ngay_het_han_thi_thuc',
            'ngay_dang_ky_tam_chu',
            'ngay_dang_ky_nhap_khau',
            'install_app',
            'total_apartment' => function($model){
                return $model->getTotalApartment();
            },
            'history_resident_map_apartments' => function($model){
                return HistoryResidentMapApartmentResponse::find()->where(['apartment_id' => $model->apartment_id, 'resident_user_phone' => $model->resident_user_phone])->orderBy(['id' => SORT_DESC])->all();
            },
            'created_at',
            'deleted_at',
            'is_check_cmtnd',
        ];
    }
}
