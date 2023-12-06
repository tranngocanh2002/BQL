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
 *   @SWG\Xml(name="ServiceDebtResponse")
 * )
 */
class ServiceDebtResponse extends ServiceDebt
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="building_area_id", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="resident_user_name", type="string", description="chủ hộ"),
     * @SWG\Property(property="early_debt", type="integer", description="nợ đầu kỳ"),
     * @SWG\Property(property="end_debt", type="integer", description="nợ cuối kỳ"),
     * @SWG\Property(property="receivables", type="integer", description="nợ phải thu"),
     * @SWG\Property(property="collected", type="integer", description="nợ đã thu"),
     * @SWG\Property(property="month", type="integer"),
     * @SWG\Property(property="status", type="integer", description="0 - không nợ, 1 - còn nợ, 2 - nhắc nợ lần 1, 3 - nhắc nợ lần 2, 4 - nhắc nợ lần 3"),
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
            'early_debt',
            'end_debt',
            'receivables',
            'collected',
            'month',
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
