<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\CardManagementMapService;
use common\models\ServiceManagementVehicle;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="CardManagementMapServiceForm")
 * )
 */
class CardManagementMapServiceForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Id card", default=1, type="integer")
     * @var integer
     */
    public $card_management_id;

    public $building_cluster_id;

    /**
     * @SWG\Property(description="Status - 0 : chưa kích hoạt, 1 - đã kích hoạt, 2- đã hủy")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="Type - 0 - thẻ cư dân, 1- thẻ xe")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="id map tương ứng với từng type : 0 -> id resident user, 1 -> service_management_vehicle id xe")
     * @var integer
     */
    public $service_management_id;

    public $expiry_time;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['card_management_id', 'building_cluster_id', 'service_management_id'], 'required', "on" => ['create']],
            [['id'], 'required', "on" => ['update', 'delete']],
            [['status' ,'type', 'card_management_id', 'expiry_time', 'building_cluster_id', 'service_management_id'], 'integer'],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $item = new CardManagementMapService();
            $item->load(CUtils::arrLoad($this->attributes), '');
            $item->status = CardManagementMapService::STATUS_ACTIVE;
            if($item->type == CardManagementMapService::TYPE_PARKING){
                $serviceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $item->service_management_id, 'building_cluster_id' => $item->building_cluster_id]);
                if(!empty($serviceManagementVehicle)){
                    $item->expiry_time = $serviceManagementVehicle->end_date;
                }
            }
            if (!$item->save()) {
                $transaction->rollBack();
                Yii::error($item->getErrors());
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            $transaction->commit();
            return CardManagementMapServiceResponse::findOne(['id' =>$item->id]);
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => Yii::t('resident', "System busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                // 'errors' => $ex->getMessage()
            ];
        }
    }

    public function update()
    {
        $item = CardManagementMapServiceResponse::findOne(['id' => (int)$this->id]);
        if ($item) {
            $item->load(CUtils::arrLoad($this->attributes), '');
            $item->status = CardManagementMapService::STATUS_ACTIVE;
            if($item->type == CardManagementMapService::TYPE_PARKING){
                $serviceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $item->service_management_id, 'building_cluster_id' => $item->building_cluster_id]);
                if(!empty($serviceManagementVehicle)){
                    $item->expiry_time = $serviceManagementVehicle->end_date;
                }
            }
            if (!$item->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            return $item;
        } else {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function delete()
    {
        if(!$this->id){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $item = CardManagementMapService::findOne($this->id);
        if($item->delete()){
            return [
                'success' => true,
                'message' => Yii::t('resident', "Delete Success")
            ];
        }else{
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
