<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\CardManagement;
use common\models\CardManagementMapService;
use common\models\EparkingCardHistory;
use common\models\ServiceManagementVehicle;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="EparkingCardEventForm")
 * )
 */
class EparkingCardEventForm extends Model
{
    /**
     * @SWG\Property(description="mã thẻ", type="string")
     * @var string
     */
    public $serial;

    /**
     * @SWG\Property(description="Loại xe", default=1, type="integer")
     * @var integer
     */
    public $vehicle_type;

    /**
     * @SWG\Property(description="Loại thẻ: 1 - thử từ, 2 - thử rfid", default=1, type="integer")
     * @var integer
     */
    public $card_type;

    /**
     * @SWG\Property(description="loại khách: 1 khách vãng lai, 2 cư dân", default=1, type="integer")
     * @var integer
     */
    public $ticket_type;

    /**
     * @SWG\Property(description="Giờ vào", default="Y/m/d H:i:s", type="string")
     * @var string
     */
    public $datetime_in;

    /**
     * @SWG\Property(description="Biển nhận diện khi vào")
     * @var string
     */
    public $plate_in;

    /**
     * @SWG\Property(description="Link ảnh khi vào (ảnh 1)")
     * @var string
     */
    public $image1_in;

    /**
     * @SWG\Property(description="Link ảnh khi vào (ảnh 2)")
     * @var string
     */
    public $image2_in;

    /**
     * @SWG\Property(description="Giờ ra", default="Y/m/d H:i:s", type="string")
     * @var string
     */
    public $datetime_out;

    /**
     * @SWG\Property(description="Biển nhận diện khi ra")
     * @var string
     */
    public $plate_out;

    /**
     * @SWG\Property(description="Link ảnh khi ra (ảnh 1)")
     * @var string
     */
    public $image1_out;

    /**
     * @SWG\Property(description="Link ảnh khi ra (ảnh 2)")
     * @var string
     */
    public $image2_out;

    /**
     * @SWG\Property(description="Status: P - xe đang trong bãi, C xe ra ngoài bãi")
     * @var string
     */
    public $status;

    public $service_management_vehicle_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['serial'], 'required'],
            [['serial', 'datetime_in', 'plate_in', 'image1_in', 'image2_in', 'datetime_out', 'plate_out', 'image1_out', 'image2_out', 'status'], 'string'],
            [['service_management_vehicle_id', 'vehicle_type', 'card_type', 'ticket_type'], 'integer'],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $item = new EparkingCardHistory();
            $item->load(CUtils::arrLoad($this->attributes), '');
            if($item->status == 'P'){
                $item->status = EparkingCardHistory::STATUS_P;
            }else{
                $item->status = EparkingCardHistory::STATUS_C;
            }
            if(!empty($this->datetime_in)){
                $item->datetime_in = CUtils::convertStringToTimeStamp($this->datetime_in, "Y/m/d H:i:s");
            }
            if(!empty($this->datetime_out)){
                $item->datetime_out = CUtils::convertStringToTimeStamp($this->datetime_out, "Y/m/d H:i:s");
            }

            if(!empty($this->serial)){
                $cardManagement = CardManagement::findOne(['number' => $this->serial]);
                if(!empty($cardManagement)){
                    $cardManagementMapService = CardManagementMapService::findOne(['card_management_id' => $cardManagement->id]);
//                    $serviceManagementVehicle = ServiceManagementVehicle::findOne(['number' => $this->serial]);
                    if(!empty($cardManagementMapService)){
                        $item->service_management_vehicle_id = $cardManagementMapService->id;
                    }
                    $item->building_cluster_id = $cardManagement->building_cluster_id;
                    $item->apartment_id = $cardManagement->apartment_id;
                }
            }

            if (!$item->save()) {
                $transaction->rollBack();
                Yii::error($item->getErrors());
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Success")
            ];
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
