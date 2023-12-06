<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\MaintenanceDevice;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="MaintenanceDeviceCreateForm")
 * )
 */
class MaintenanceDeviceCreateForm extends Model
{
    /**
     * @SWG\Property(description="Id - Bắt buộc khi update", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Status: 0 - ngừng hoạt động, 1 - đang hoạt động", default=1, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="name: tên thiết bị")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="code: mã thiết bị")
     * @var string
     */
    public $code;

    /**
     * @SWG\Property(description="position: vị trí thiết bị")
     * @var string
     */
    public $position;

    /**
     * @SWG\Property(description="description: mô tả")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(property="attach", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $attach;

    /**
     * @SWG\Property(description="guarantee_time_start: thời gian bắt đầu bảo hành")
     * @var integer
     */
    public $guarantee_time_start;

    /**
     * @SWG\Property(description="guarantee_time_end: thời gian kết thúc bảo hành")
     * @var integer
     */
    public $guarantee_time_end;

    /**
     * @SWG\Property(description="maintenance_time_start: thời gian bắt đầu bảo trì")
     * @var integer
     */
    public $maintenance_time_start;

    /**
     * @SWG\Property(description="type: loại thiết bị 0 - máy tính, 1 - quạt, 2 - camera, 3 - đèn, 4 - thang máy")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="cycle: chu kỳ bảo trì 0- không lặp lại, 1 - 1 tháng, 2 - 2 tháng, 3 - 3 tháng, 6 - 6 tháng, 12 - 12 tháng, 24 - 24 tháng")
     * @var integer
     */
    public $cycle;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update']],
            [['name', 'code',  'position', 'description'], 'string'],
            [['id', 'status', 'guarantee_time_start', 'guarantee_time_end', 'maintenance_time_start', 'type', 'cycle'], 'integer'],
            [['attach'], 'safe'],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $item = new MaintenanceDevice();
            $item->load(CUtils::arrLoad($this->attributes), '');
            $checkCode = MaintenanceDevice::findOne(['building_cluster_id' => $user->building_cluster_id, 'code' => $item->code, 'is_deleted' => MaintenanceDevice::NOT_DELETED]);
            if(!empty($checkCode)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Mã thiết bị đã tồn tại"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            if (isset($this->attach) && is_array($this->attach)) {
                $item->attach = json_encode($this->attach);
            }
            $item->status = MaintenanceDevice::STATUS_ON;
            $item->building_cluster_id = $user->building_cluster_id;
            if (!$item->save()) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            $transaction->commit();
            return MaintenanceDeviceResponse::findOne(['id' => $item->id]);
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function update()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $item = MaintenanceDevice::findOne(['id' => (int)$this->id, 'building_cluster_id' => $user->building_cluster_id, 'is_deleted' => MaintenanceDevice::NOT_DELETED]);
            if ($item) {
                $item->load(CUtils::arrLoad($this->attributes), '');
                $checkCode = MaintenanceDevice::find()->where([
                    'building_cluster_id' => $user->building_cluster_id,
                    'code' => $item->code,
                    'is_deleted' => MaintenanceDevice::NOT_DELETED
                ])->andWhere(['<>', 'id', $item->id])->one();
                if(!empty($checkCode)){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Mã thiết bị đã tồn tại"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                }
                if (isset($this->attach) && is_array($this->attach)) {
                    $item->attach = json_encode($this->attach);
                }
                if (!$item->save()) {
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                }
                $transaction->commit();
                return MaintenanceDeviceResponse::findOne(['id' => $item->id]);
            } else {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
