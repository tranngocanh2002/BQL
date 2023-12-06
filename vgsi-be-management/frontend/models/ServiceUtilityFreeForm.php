<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceMapManagement;
use common\models\ServiceUtilityConfig;
use common\models\ServiceUtilityFree;
use common\models\ServiceUtilityPrice;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityFreeForm")
 * )
 */
class ServiceUtilityFreeForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="name", default="DV 1", type="string")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="name en", default="DV 1", type="string")
     * @var string
     */
    public $name_en;

    /**
     * @SWG\Property(description="description", default="", type="string")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(description="regulation", default="", type="string", description="Quy định")
     * @var string
     */
    public $regulation;

    /**
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $medias;

    /**
     * @SWG\Property(description="hotline", default="", type="string")
     * @var string
     */
    public $hotline;

    /**
     * @SWG\Property(description="hours_open - giờ mở của", default="08:30", type="string")
     * @var string
     */
    public $hours_open;

    /**
     * @SWG\Property(description="hours_close - giờ đóng cửa", default="23:00", type="string")
     * @var string
     */
    public $hours_close;

    /**
     * @SWG\Property(description="status: -1 Dừng hoạt động, 0 Tạm dừng hoạt động, 1 Đang hoạt động", default=0, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="service map management id", default=0, type="integer")
     * @var integer
     */
    public $service_map_management_id;

    /**
     * @SWG\Property(description="booking_type: 0 - đặt theo lượt, 1 - đặt theo slot, 2 - đặt theo khung giờ", default=0, type="integer")
     * @var integer
     */
    public $booking_type;

    /**
     * @SWG\Property(description="timeout_cancel_book: Thời gian tối thiểu trước để hủy được book", default=10, type="integer")
     * @var integer
     */
    public $timeout_cancel_book;

    /**
     * @SWG\Property(description="timeout_pay_request: Thời gian tối đa chờ tạo yêu cầu thanh toán", default=120, type="integer")
     * @var integer
     */
    public $timeout_pay_request;

    /**
     * @SWG\Property(description="limit_book_apartment: Giới hạn lượt book của căn hộ trong 1 tháng", default=10, type="integer")
     * @var integer
     */
    public $limit_book_apartment;


    /**
     * @SWG\Property(description="deposit_money: Số tiền đặt cọc nếu dịch vụ cần đặt cọc", default=0, type="integer")
     * @var integer
     */
    public $deposit_money;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update', 'delete']],
//            [['name', 'service_map_management_id'], 'required'],
            [['id', 'service_map_management_id', 'booking_type', 'timeout_pay_request', 'timeout_cancel_book', 'limit_book_apartment', 'deposit_money', 'status'], 'integer'],
            [['name', 'name_en', 'description', 'hours_open', 'hours_close', 'hotline', 'regulation'], 'string'],
            [['medias'], 'safe'],
            [['service_map_management_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceMapManagement::className(), 'targetAttribute' => ['service_map_management_id' => 'id']],
            [['hours_open'], 'validateHoursOpenClose'],
            [['hours_close'], 'validateHoursOpenClose']
        ];
    }

    public function validateHoursOpenClose($attribute, $params, $validator)
    {
        $hours_open = preg_replace( '/[^0-9]/', '', $this->hours_open);
        $hours_close = preg_replace( '/[^0-9]/', '', $this->hours_close);
        if(empty($this->hours_open) || empty($this->hours_close) || ((int)$hours_open >= (int)$hours_close)){
            $this->addError($attribute, Yii::t('frontend', 'Khung giờ mở cửa không hợp lệ'));
        }
        if(!empty($this->id)){
            $serviceUtilityPrices = ServiceUtilityPrice::find()->where(['service_utility_free_id' => $this->id])->all();
            foreach ($serviceUtilityPrices as $serviceUtilityPrice){
                $output_start_time = preg_replace( '/[^0-9]/', '', $serviceUtilityPrice->start_time);
                $output_end_time = preg_replace( '/[^0-9]/', '', $serviceUtilityPrice->end_time);
                if((int)$output_start_time < (int)$hours_open){
                    $this->addError($attribute, Yii::t('frontend', 'Dịch vụ đang tồn tại cấu hình thời gian đặt chỗ nhỏ hơn thời gian mở cửa'));
                }else if((int)$output_end_time > (int)$hours_close){
                    $this->addError($attribute, Yii::t('frontend', 'Dịch vụ đang tồn tại cấu hình thời gian đặt chỗ lớn hơn thời gian đóng cửa'));
                }
            }
        }
    }

    public function create()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $serviceMapManagement = ServiceMapManagement::findOne(['id' => $this->service_map_management_id, 'is_deleted' => ServiceMapManagement::NOT_DELETED, 'building_cluster_id' => $buildingCluster->id]);
        if(empty($serviceMapManagement)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $ServiceUtilityFree = new ServiceUtilityFree();
        $ServiceUtilityFree->load(CUtils::arrLoad($this->attributes), '');
        if(!isset($this->status)){
            $ServiceUtilityFree->status = ServiceUtilityFree::STATUS_ACTIVE;
        }
        $ServiceUtilityFree->building_cluster_id = $buildingCluster->id;
        $ServiceUtilityFree->building_area_id = $serviceMapManagement->building_area_id;
        $ServiceUtilityFree->service_id = $serviceMapManagement->service_id;
        $ServiceUtilityFree->generateCode();
        if (isset($this->medias) && is_array($this->medias)) {
            $ServiceUtilityFree->medias = !empty($this->medias) ? json_encode($this->medias) : null;
        }
        if (!$ServiceUtilityFree->save()) {
            Yii::error($ServiceUtilityFree->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceUtilityFree->getErrors()
            ];
        } else {
            return ServiceUtilityFreeResponse::findOne(['id' => $ServiceUtilityFree->id]);
        }
    }

    public function update()
    {
        $ServiceUtilityFree = ServiceUtilityFreeResponse::findOne(['id' => (int)$this->id]);
        if ($ServiceUtilityFree) {
            $ServiceUtilityFree->load(CUtils::arrLoad($this->attributes), '');
            if (isset($this->medias) && is_array($this->medias)) {
                $ServiceUtilityFree->medias = !empty($this->medias) ? json_encode($this->medias) : null;
            }
            if (!$ServiceUtilityFree->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceUtilityFree->getErrors()
                ];
            } else {
                return $ServiceUtilityFree;
            }
        } else {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function delete()
    {
        if(!$this->id){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        //check sử dụng
        $serviceUtilityConfig = ServiceUtilityConfig::findOne(['service_utility_free_id' => $this->id]);
        if(!empty($serviceUtilityConfig)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Service Utility Free Using, Not Delete"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $ServiceUtilityFree = ServiceUtilityFree::findOne($this->id);
        if($ServiceUtilityFree->delete()){
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Delete Success")
            ];
        }else{
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

}
