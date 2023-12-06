<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingCluster;
use common\models\ServiceMapManagement;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;
use Da\QrCode\QrCode;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentMapResidentUserFullResponse")
 * )
 */

class ApartmentMapResidentUserFullResponse extends ApartmentMapResidentUser
{
    /**
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_capacity", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_code", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="apartment_short_name", type="string"),
     * @SWG\Property(property="resident_user_id", type="integer"),
     * @SWG\Property(property="resident_user_email", type="string"),
     * @SWG\Property(property="resident_user_first_name", type="string"),
     * @SWG\Property(property="resident_user_last_name", type="string"),
     * @SWG\Property(property="resident_user_phone", type="string"),
     * @SWG\Property(property="resident_user_avatar", type="string"),
     * @SWG\Property(property="resident_user_nationality", type="string"),
     * @SWG\Property(property="resident_user_birthday", type="integer"),
     * @SWG\Property(property="resident_user_gender", type="integer"),
     * @SWG\Property(property="type_relationship", type="integer"),
     * @SWG\Property(property="resident_type_relationship", type="integer"),
     * @SWG\Property(property="resident_type_relationship_name", type="string"),
     * @SWG\Property(property="resident_type_relationship_name_en", type="string"),
     * @SWG\Property(property="type", type="integer", description="0 - thành viên, 1 - chủ hộ, 2 - khách"),
     * @SWG\Property(property="resident_is_household", type="integer", description="0 - thành viên, 1 - chủ hộ, 2 - khách"),
     * @SWG\Property(property="resident_user_cmtnd", type="string", description="CMD ND"),
     * @SWG\Property(property="resident_user_ngay_cap_cmtnd", type="string", description="ngày cấp cmtnd"),
     * @SWG\Property(property="resident_user_noi_cap_cmtnd", type="string", description="nơi cấp cmtnd"),
     * @SWG\Property(property="created_at", type="string", description="Ngày gia nhập căn hộ"),
     */
    public function fields()
    {
        return [
            'apartment_id',
            'apartment_capacity',
            'apartment_name',
            'apartment_code',
            'apartment_parent_path',
            'apartment_short_name',
            'resident_user_id',
            'resident_user_phone',
            'resident_user_email',
            'resident_user_first_name',
            'resident_user_last_name',
            'resident_name_search',
            'resident_user_gender',
            'resident_user_birthday',
            'resident_user_avatar',
            'resident_user_nationality',
            'type_relationship',
            'resident_type_relationship' => function ($model) {
                return $model->type_relationship;
            },
            'resident_type_relationship_name' => function ($model) {
                return ApartmentMapResidentUser::$type_relationship_list[$model->type_relationship] ?? null;
            },
            'resident_type_relationship_name_en' => function ($model) {
                return ApartmentMapResidentUser::$type_relationship_en_list[$model->type_relationship] ?? null;
            },
            'type',
            'resident_is_household' => function($model){
                return $model->type;
            },
            'resident_user_cmtnd' => function($model){
                return $model->cmtnd;
            },
            'resident_user_ngay_cap_cmtnd' => function($model){
                return $model->ngay_cap_cmtnd;
            },
            'resident_user_noi_cap_cmtnd' => function($model){
                return $model->noi_cap_cmtnd;
            },
            'created_at'
        ];
    }
}
