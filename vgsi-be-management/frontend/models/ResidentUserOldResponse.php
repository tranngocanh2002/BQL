<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\HistoryResidentMapApartment;
use common\models\ResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserOldResponse")
 * )
 */
class ResidentUserOldResponse extends ResidentUser
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="phone", type="string"),
     * @SWG\Property(property="email", type="string"),
     * @SWG\Property(property="first_name", type="string"),
     * @SWG\Property(property="last_name", type="string"),
     * @SWG\Property(property="avatar", type="string"),
     * @SWG\Property(property="gender", type="integer"),
     * @SWG\Property(property="birthday", type="integer"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="status_verify_phone", type="integer"),
     * @SWG\Property(property="status_verify_email", type="integer"),
     * @SWG\Property(property="apartments", type="array",
     *     @SWG\Items(type="object",
     *          @SWG\Property(property="apartment_id", type="integer", description="Id căn hộ"),
     *          @SWG\Property(property="apartment_name", type="string", description="Tên căn hộ"),
     *          @SWG\Property(property="apartment_parent_path", type="string", description="Cụm căn hộ"),
     *          @SWG\Property(property="type", type="integer", description="0 - thành viên, 1 - chủ hộ"),
     *          @SWG\Property(property="time_in", type="integer", description="Thời điểm được thêm vào căn hộ"),
     *          @SWG\Property(property="time_out", type="integer", description="Thời điểm bị loại ra khỏi căn hộ"),
     *      ),
     * ),
     * @SWG\Property(property="cmtnd", type="string"),
     * @SWG\Property(property="ngay_cap_cmtnd", type="integer"),
     * @SWG\Property(property="noi_cap_cmtnd", type="string"),
     * @SWG\Property(property="nationality", type="string"),
     * @SWG\Property(property="work", type="string"),
     * @SWG\Property(property="so_thi_thuc", type="string"),
     * @SWG\Property(property="ngay_het_han_thi_thuc", type="integer"),
     * @SWG\Property(property="ngay_dang_ky_tam_chu", type="integer"),
     * @SWG\Property(property="ngay_dang_ky_nhap_khau", type="integer"),
     */


    public function fields()
    {
        return [
            'id',
            'phone',
            'email',
            'first_name',
            'first_name',
            'avatar',
            'gender' => function($model){
                if(empty($model->gender)){
                    return ResidentUser::GENDER_1;
                }
                return $model->gender;
            },
            'birthday',
            'status',
            'status_verify_phone',
            'status_verify_email',
            'apartments' => function ($model) {
                $res = [];
                $historyMaps = HistoryResidentMapApartment::find()->where(['resident_user_id' => $model->id])->all();
                foreach ($historyMaps as $historyMap){
                    $res[] = [
                        'type' => $historyMap->type,
                        'apartment_id' => $historyMap->apartment_id,
                        'apartment_name' => $historyMap->apartment_name,
                        'time_in' => $historyMap->time_in,
                        'time_out' => $historyMap->time_out,
                        'apartment_parent_path' => trim($historyMap->apartment_parent_path, '/'),
                    ];
                }
                return $res;
            },
            'cmtnd',
            'ngay_cap_cmtnd',
            'noi_cap_cmtnd',
            'nationality',
            'work',
            'so_thi_thuc',
            'ngay_het_han_thi_thuc',
            'ngay_dang_ky_tam_chu',
            'ngay_dang_ky_nhap_khau',
        ];
    }
}
