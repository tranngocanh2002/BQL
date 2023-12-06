<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserResponse")
 * )
 */
class ResidentUserResponse extends ResidentUser
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="phone", type="string"),
     * @SWG\Property(property="email", type="string"),
     * @SWG\Property(property="display_name", type="string", description="Tên hiển thị , có thể sửa"),
     * @SWG\Property(property="first_name", type="string"),
     * @SWG\Property(property="last_name", type="string"),
     * @SWG\Property(property="avatar", type="string"),
     * @SWG\Property(property="gender", type="integer"),
     * @SWG\Property(property="birthday", type="integer"),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="status_verify_phone", type="integer"),
     * @SWG\Property(property="status_verify_email", type="integer"),
     * @SWG\Property(property="is_send_email", type="integer"),
     * @SWG\Property(property="is_send_notify", type="integer"),
     * @SWG\Property(property="notify_tags", type="array",
     *     @SWG\Items(type="string", default= "BUILDING_CLUSTER_1"),
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
     * @SWG\Property(property="is_deleted", type="integer", description="0 chưa xóa, 1 đã xóa"),
     * @SWG\Property(property="deleted_at", type="integer", description="thời điểm báo "),
     * @SWG\Property(property="reason", type="string", description="lý do xóa"),
     */


    public function fields()
    {
        return [
            'id',
            'phone',
            'email',
            'display_name',
            'first_name',
            'last_name',
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
            'is_send_email',
            'is_send_notify',
            'notify_tags' => function ($model) {
                return (!empty($model->notify_tags)) ? json_decode($model->notify_tags, true) : [];
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
            'is_deleted',
            'deleted_at',
            'reason',
        ];
    }
}
