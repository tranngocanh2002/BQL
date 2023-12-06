<?php

namespace frontend\models;

use common\models\MaintenanceDevice;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="MaintenanceDeviceResponse")
 * )
 */
class MaintenanceDeviceResponse extends MaintenanceDevice
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="code", type="string"),
     * @SWG\Property(property="position", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="status", type="integer", description="0 : ngừng hoạt động, 1: đang hoạt động"),
     * @SWG\Property(property="type", type="integer"),
     * @SWG\Property(property="cycle", type="integer"),
     * @SWG\Property(property="attach", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="guarantee_time_start", type="integer"),
     * @SWG\Property(property="guarantee_time_end", type="integer"),
     * @SWG\Property(property="maintenance_time_start", type="integer"),
     * @SWG\Property(property="maintenance_time_last", type="integer", description="ngày bảo trì gần nhất"),
     * @SWG\Property(property="maintenance_time_next", type="integer", description="ngày bảo trì sắp tới"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'code',
            'position',
            'description',
            'attach' => function ($model) {
                return (!empty($model->attach)) ? json_decode($model->attach) : null;
            },
            'status',
            'type',
            'cycle',
            'guarantee_time_start',
            'guarantee_time_end',
            'maintenance_time_start',
            'maintenance_time_last',
            'maintenance_time_next',
            'created_at',
            'updated_at',
        ];
    }
}
