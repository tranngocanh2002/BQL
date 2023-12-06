<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="EparkingCustomerResponse")
 * )
 */
class EparkingCustomerResponse extends ApartmentMapResidentUser
{
    /**
     * @SWG\Property(property="customer_id", type="integer", description="id resident user"),
     * @SWG\Property(property="phone", type="string"),
     * @SWG\Property(property="email", type="string"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="address", type="integer"),
     * @SWG\Property(property="identity", type="string", description="cmt"),
     * @SWG\Property(property="status", type="integer", description="status: 1 - active"),
     * @SWG\Property(property="room", type="string", description="Tên căn hộ"),
     * @SWG\Property(property="building", type="string", description="Thông tin cụm và building area"),
     */

    public function fields()
    {
        return [
            'customer_id' => function ($model) {
                return $model->resident_user_id;
            },
            'phone' => function ($model) {
                return $model->resident_user_phone;
            },
            'email' => function ($model) {
                return $model->resident_user_email;
            },
            'name' => function ($model) {
                return $model->resident_user_first_name . ' ' . $model->resident_user_last_name;
            },
            'address' => function ($model) {
                return '';
            },
            'identity' => function ($model) {
                if (!empty($model->resident)) {
                    return $model->resident->cmtnd;
                }
                return '';
            },
            'status',
            'room' => function($model){
                return $model->apartment_name;
            },
            'building' => function ($model) {
                return trim($model->apartment_parent_path, '/');
            },
        ];
    }
}
