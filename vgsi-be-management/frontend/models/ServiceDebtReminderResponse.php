<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceDebt;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceDebtReminderResponse")
 * )
 */
class ServiceDebtReminderResponse extends ServiceDebt
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="building_area_id", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="resident_user_name", type="string", description="chủ hộ"),
     * @SWG\Property(property="resident_user_email", type="string", description="email"),
     * @SWG\Property(property="resident_user_phone", type="string", description="phone"),
     * @SWG\Property(property="resident_user_app", type="integer", description="app"),
     * @SWG\Property(property="end_debt", type="integer", description="nợ cuối kỳ"),
     * @SWG\Property(property="status", type="integer", description="0 - không nợ, 1 - còn nợ, 2 - thông báo phí, 3 - nhắc nợ lần 1, 4 - nhắc nợ lần 2, 5 - nhắc nợ lần 3, 6 - thông báo tạm dừng dịch vụ"),
     * @SWG\Property(property="status_name", type="string"),
     * @SWG\Property(property="status_color", type="string"),
     * @SWG\Property(property="created_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'building_area_id',
            'apartment_id',
            'apartment_name' => function ($model) {
                if (!empty($model->apartment)) {
                    return $model->apartment->name;
                }
                return '';
            },
            'apartment_parent_path' => function ($model) {
                if (!empty($model->apartment)) {
                    return trim($model->apartment->parent_path, '/');
                }
                return '';
            },
            'resident_user_name' => function ($model) {
                if (!empty($model->apartment)) {
                    return $model->apartment->resident_user_name;
                }
                return '';
            },
            'resident_user_email' => function ($model) {
                if (!empty($model->apartment)) {
                    if (!empty($model->apartment->residentUser)) {
                        return $model->apartment->residentUser->email;
                    }
                }
                return '';
            },
            'resident_user_phone' => function ($model) {
                if (!empty($model->apartment)) {
                    if (!empty($model->apartment->residentUser)) {
                        return $model->apartment->residentUser->phone;
                    }
                }
                return '';
            },
            'resident_user_app' => function($model){
                if (!empty($model->apartment)) {
                    if (!empty($model->apartment->residentUser)) {
                        return $model->apartment->residentUser->active_app;
                    }
                }
                return 0;
            },
            'end_debt',
            'status',
            'status_name' => function ($model) {
                return isset(ServiceDebt::$status_lst[$model->status]) ? ServiceDebt::$status_lst[$model->status] : "";
            },
            'status_color' => function ($model) {
                return isset(ServiceDebt::$status_color[$model->status]) ? ServiceDebt::$status_color[$model->status] : "";
            },
            'created_at',
        ];
    }
}
