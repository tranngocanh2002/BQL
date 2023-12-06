<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\MaintenanceDevice;
use common\models\ManagementUserNotify;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="MaintenanceDeviceConfirmationForm")
 * )
 */
class MaintenanceDeviceConfirmationForm extends Model
{

    /**
     * @SWG\Property(description="id_array : mảng các id cần xác nhận", type="array",
     *      @SWG\Items(type="integer", default=0),
     * )
     * @var array
     */
    public $id_array;

    /**
     * @SWG\Property(description="maintenance_time_last: ngày bảo trì")
     * @var integer
     */
    public $maintenance_time_last;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_array', 'maintenance_time_last'], 'required'],
            [['maintenance_time_last'], 'integer'],
            [['id_array'], 'safe'],
        ];
    }

    public function isConfirmation()
    {
        try {
            $user = Yii::$app->user->getIdentity();
            if ((empty($this->id_array) || !is_array($this->id_array))) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $maintenanceDevices = MaintenanceDevice::find()->where([
                'id' => $this->id_array,
                'building_cluster_id' => $user->building_cluster_id,
                'is_deleted' => MaintenanceDevice::NOT_DELETED,
            ])->all();
            foreach ($maintenanceDevices as $maintenanceDevice){
                $maintenanceDevice->maintenance_time_last = $this->maintenance_time_last;
                $maintenanceDevice->save();
            }
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Update success"),
            ];
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

}
