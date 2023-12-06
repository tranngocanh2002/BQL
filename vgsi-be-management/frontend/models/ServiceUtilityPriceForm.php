<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
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
 *   @SWG\Xml(name="ServiceUtilityPriceForm")
 * )
 */
class ServiceUtilityPriceForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;
    
    /**
     * @SWG\Property(description="service utility config id", default=1, type="integer")
     * @var integer
     */
    public $service_utility_config_id;

    /**
     * @SWG\Property(description="start time : thời gian bắt đầu", default="08:00", type="string")
     * @var string
     */
    public $start_time;

    /**
     * @SWG\Property(description="end time : thời gian kết thúc", default="23:00", type="string")
     * @var string
     */
    public $end_time;

    /**
     * @SWG\Property(description="price_hourly: giá tính theo 1 giờ", default=0, type="integer")
     * @var integer
     */
    public $price_hourly;

    /**
     * @SWG\Property(description="price_adult: giá tính theo 1 lượt người lớn", default=0, type="integer")
     * @var integer
     */
    public $price_adult;

    /**
     * @SWG\Property(description="price_child: giá tính theo 1 lượt trẻ em", default=0, type="integer")
     * @var integer
     */
    public $price_child;

    public $_ServiceUtilityConfig;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update', 'delete']],
            [['service_utility_config_id', 'start_time', 'end_time'], 'required'],
            [['id', 'service_utility_config_id', 'price_hourly', 'price_adult', 'price_child'], 'integer'],
            [['start_time', 'end_time'], 'string'],
            [['start_time'], 'validateTimeSet']
        ];
    }

    public function validateTimeSet($attribute, $params, $validator)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $ServiceUtilityConfig = ServiceUtilityConfig::findOne(['building_cluster_id' => $buildingCluster->id, 'id' => $this->service_utility_config_id]);
        if(empty($ServiceUtilityConfig)){
            $this->addError($attribute, Yii::t('frontend', 'ServiceUtilityConfig Not Exist'));
        }
        $this->_ServiceUtilityConfig = $ServiceUtilityConfig;

        //check start_time end_time
        $output_start_time = preg_replace( '/[^0-9]/', '', $this->start_time);
        $output_end_time = preg_replace( '/[^0-9]/', '', $this->end_time);
        if((int)$output_start_time >= (int)$output_end_time){
            $this->addError($attribute, Yii::t('frontend', 'Invalid data time'));
        }

        if(empty($this->_ServiceUtilityConfig->serviceUtilityFree)){
            $this->addError($attribute, Yii::t('frontend', 'ServiceUtilityFree Not Exist'));
        }
        $open_time = preg_replace( '/[^0-9]/', '', $this->_ServiceUtilityConfig->serviceUtilityFree->hours_open);
        $close_time = preg_replace( '/[^0-9]/', '', $this->_ServiceUtilityConfig->serviceUtilityFree->hours_close);
        if((int)$output_start_time < (int)$open_time){
            $this->addError($attribute, Yii::t('frontend', 'Thời gian bắt đầu nhỏ hơn thời gian mở của của dịch vụ'));
        }
        if((int)$output_end_time > (int)$close_time){
            $this->addError($attribute, Yii::t('frontend', 'Thời gian kết thúc lớn hơn thời gian đóng cửa của dịch vụ'));
        }
        //check trong khoảng giờ
        $ServiceUtilityPrices = ServiceUtilityPrice::find()
            ->where(['service_utility_config_id' => $this->service_utility_config_id]);
        if(isset($this->id)){
            $ServiceUtilityPrices = $ServiceUtilityPrices->andWhere(['<>', 'id', (int)$this->id]);
        }
        $ServiceUtilityPrices = $ServiceUtilityPrices->all();
        foreach ($ServiceUtilityPrices as $ServiceUtilityPrice){
            $indb_start_time = preg_replace( '/[^0-9]/', '', $ServiceUtilityPrice->start_time);
            $indb_end_time = preg_replace( '/[^0-9]/', '', $ServiceUtilityPrice->end_time);
            if(
                (
                    ($indb_start_time <= $output_start_time)
                    && ($output_start_time < $indb_end_time)
                )
                || (
                    ($indb_start_time < $output_end_time)
                    && ($output_end_time <= $indb_end_time)
                )
                || (
                    ($output_end_time > $indb_end_time)
                    && ($output_start_time < $indb_start_time)
                )
            ){
                $this->addError($attribute, Yii::t('frontend', 'Invalid data time'));
            }
        }

    }

    public function create()
    {
        $ServiceUtilityPrice = new ServiceUtilityPrice();
        $ServiceUtilityPrice->load(CUtils::arrLoad($this->attributes), '');
        $ServiceUtilityPrice->building_cluster_id = $this->_ServiceUtilityConfig->building_cluster_id;
        $ServiceUtilityPrice->service_utility_free_id = $this->_ServiceUtilityConfig->service_utility_free_id;
        $ServiceUtilityPrice->service_utility_config_id = $this->_ServiceUtilityConfig->id;
        if (!$ServiceUtilityPrice->save()) {
            Yii::error($ServiceUtilityPrice->getErrors());
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceUtilityPrice->getErrors()
            ];
        } else {
            return ServiceUtilityPriceResponse::findOne(['id' => $ServiceUtilityPrice->id]);
        }
    }

    public function update()
    {
        $ServiceUtilityPrice = ServiceUtilityPriceResponse::findOne(['id' => (int)$this->id]);
        if ($ServiceUtilityPrice) {
            $ServiceUtilityPrice->load(CUtils::arrLoad($this->attributes), '');

            if (!$ServiceUtilityPrice->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $ServiceUtilityPrice->getErrors()
                ];
            } else {
                return $ServiceUtilityPrice;
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
        $ServiceUtilityPrice = ServiceUtilityPrice::findOne($this->id);
        if($ServiceUtilityPrice->delete()){
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
