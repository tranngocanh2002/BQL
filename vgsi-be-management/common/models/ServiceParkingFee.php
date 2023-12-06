<?php

namespace common\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_parking_fee".
 *
 * @property int $id
 * @property int $service_map_management_id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property int $count_month số tháng gửi tiếp theo
 * @property int $start_time
 * @property int $end_time
 * @property int $service_parking_level_id
 * @property int $service_management_vehicle_id
 * @property int $total_money
 * @property int $status 0 - chưa duyệt, 1 - đã duyệt
 * @property string $description
 * @property string $description_en
 * @property string $json_desc
 * @property int $is_created_fee
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $service_payment_fee_id
 * @property int $fee_of_month
 *
 * @property Apartment $apartment
 * @property ServicePaymentFee $servicePaymentFee
 * @property BuildingCluster $buildingCluster
 * @property ServiceManagementVehicle $serviceManagementVehicle
 * @property ServiceMapManagement $serviceMapManagement
 * @property ServiceParkingLevel $serviceParkingLevel
 * @property ManagementUser $managementUser
 */
class ServiceParkingFee extends \yii\db\ActiveRecord
{
    const IS_UNCREATED_FEE = 0;
    const IS_CREATED_FEE = 1;

    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_parking_fee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_map_management_id', 'building_cluster_id', 'apartment_id', 'service_parking_level_id', 'service_management_vehicle_id'], 'required'],
            [['fee_of_month', 'service_payment_fee_id', 'service_map_management_id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'count_month', 'start_time', 'end_time', 'service_parking_level_id', 'service_management_vehicle_id', 'total_money', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_created_fee'], 'integer'],
            [['description', 'description_en', 'json_desc'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'service_map_management_id' => Yii::t('common', 'Service Map Management ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'count_month' => Yii::t('common', 'Count Month'),
            'start_time' => Yii::t('common', 'Start Time'),
            'end_time' => Yii::t('common', 'End Time'),
            'service_parking_level_id' => Yii::t('common', 'Service Parking Level ID'),
            'service_management_vehicle_id' => Yii::t('common', 'Service Management Vehicle ID'),
            'total_money' => Yii::t('common', 'Total Money'),
            'status' => Yii::t('common', 'Status'),
            'description' => Yii::t('common', 'Description'),
            'json_desc' => Yii::t('common', 'Json Desc'),
            'is_created_fee' => Yii::t('common', 'Is Created Fee'),
            'service_payment_fee_id' => Yii::t('common', 'Service Payment Fee Id'),
            'fee_of_month' => Yii::t('common', 'Fee Of Month'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'created_by' => Yii::t('common', 'Created By'),
            'updated_by' => Yii::t('common', 'Updated By'),
        ];
    }

    /**
     * @inheritdoc
     */
    function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'time',
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ]
            ],
            [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    self::EVENT_BEFORE_UPDATE => ['updated_by'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuildingCluster()
    {
        return $this->hasOne(BuildingCluster::className(), ['id' => 'building_cluster_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::className(), ['id' => 'apartment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicePaymentFee()
    {
        return $this->hasOne(ServicePaymentFee::className(), ['id' => 'service_payment_fee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceManagementVehicle()
    {
        return $this->hasOne(ServiceManagementVehicle::className(), ['id' => 'service_management_vehicle_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceMapManagement()
    {
        return $this->hasOne(ServiceMapManagement::className(), ['id' => 'service_map_management_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceParkingLevel()
    {
        return $this->hasOne(ServiceParkingLevel::className(), ['id' => 'service_parking_level_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagementUser()
    {
        return $this->hasOne(ManagementUser::className(), ['id' => 'updated_by']);
    }

    public static function getCharge($start_time, $service_parking_level_id, $count_month, $cancel_date = null)
    {
        Yii::warning(date('Y-m-d 00:00:00', $start_time));
        $start_time = strtotime(date('Y-m-d 00:00:00', $start_time));
        $start_time_start = strtotime(date('Y-m-01 00:00:00', $start_time));
        $is_le = 0;
        $current_last_month = date('Y-m-01 00:00:00', strtotime('+1 month', strtotime(date('Y-m-01', $start_time))));
        $current_last_date = date('Y-m-01 00:00:00', strtotime('+1 month', strtotime(date('Y-m-01', $start_time))));
        if(!empty($cancel_date)){
            $cancel_date = date('Y-m-d 00:00:00', $cancel_date);
            $cancel_date_end = date('Y-m-t 00:00:00', strtotime($cancel_date));
            if(strtotime($cancel_date) >= $start_time && strtotime($cancel_date) < strtotime($current_last_date)){
                if(strtotime($cancel_date) !== strtotime($cancel_date_end) && strtotime($cancel_date) >= $start_time){
                    $current_last_date = date('Y-m-d 00:00:00', strtotime('+1 day', strtotime($cancel_date)));
                    $is_le = 1;
                }else if(strtotime($cancel_date) == strtotime($cancel_date_end) && ($start_time !== $start_time_start)){
                    $current_last_date = date('Y-m-d 00:00:00', strtotime('+1 day', strtotime($cancel_date)));
                    $is_le = 1;
                }
            }
        }
        $current_day = (int)date('d', $start_time);
        if($current_day > 1){ $is_le = 1;}
        $current_month = date('m', $start_time);
        $current_year = (int)date('Y', $start_time);
        $current_year_in = (int)date('Y', time());
        $total_month = $current_month;
//        if($current_year !== $current_year_in){
        $total_month .= '/'.$current_year;
//        }
        $j = (int)$current_month;
        $t = 0;
        $is_le_last = 0;
        for ($i = 1; $i <= $count_month - 1; $i++) {
            $m = $current_month + $i;
            $j++;
            if($j > 12){
                $j = $j -12;
                $t++;
            }
            $m = $m%12;
            if($m == 0){
                $m = 12;
            }
            if($m < 10){ $m = '0'.$m;}
            if(($current_year+$t) !== $current_year_in){
                $m .= '/'.($current_year+$t);
            }else{
                $m .= '/'.$current_year;
            }
            $total_month .= ', ' . $m;

            if(!empty($cancel_date)){
//                Yii::warning(date('Y-m-d 00:00:00', strtotime('+'.$i.' month', $start_time)));
//                if(strtotime($cancel_date) <= strtotime('+'.$i.' month', $start_time)){
//                    $is_le_last = 1;
//                    Yii::warning('le 3');
//                }
                $start_time_new = strtotime('+'.$i.' month', strtotime(date('Y-m-01 00:00:00', $start_time)));
                Yii::warning($cancel_date);
                Yii::warning(date('Y-m-d', $start_time_new));
                $start_time_start = strtotime(date('Y-m-01 00:00:00', $start_time_new));
                $current_last_date_in = strtotime('+1 month', $start_time_start);
//                $cancel_date = date('Y-m-d 00:00:00', $cancel_date);
//                $cancel_date_end = date('Y-m-t 00:00:00', strtotime($cancel_date));
                if(strtotime($cancel_date) >= $start_time_new && strtotime($cancel_date) < $current_last_date_in){
                    if(strtotime($cancel_date) !== strtotime($cancel_date_end) && strtotime($cancel_date) >= $start_time_new){
                        $is_le_last = 1;
                    }else if(strtotime($cancel_date) == strtotime($cancel_date_end) && ($start_time_new !== $start_time_start)){
                        $is_le_last = 1;
                    }
                }
            }

        }
        $dateRes = [
            'total_month' => $total_month,
            'description' => '',
            'total_money' => 0,
            'end_time' => 0,
        ];
        $description = "Phí xe tháng $total_month:\n";
        $description_en = "Month parking fees $total_month:\n";
        $json_desc['month'] = [
            'text' => $description,
            'text_en' => $description_en,
            'total_month' => $total_month,
        ];
        $total_money = 0;
        $serviceParkingLevel = ServiceParkingLevel::findOne(['id' => $service_parking_level_id]);
        $x = 0;
        if($is_le == 1){
            $x = 1;
            $diff_day_in_month = date_diff(date_create(date('Y-m-01', $start_time)),date_create($current_last_month));
            $total_day_in_month = (int)$diff_day_in_month->format("%R%a");
            Yii::warning($total_day_in_month);

            $diff_day_use =date_diff(date_create(date('Y-m-d', $start_time)),date_create($current_last_date));
            $total_day_use =  (int)$diff_day_use->format("%R%a");
            $description .= $serviceParkingLevel->name . ': ' . CUtils::formatPrice($serviceParkingLevel->price/$total_day_in_month) . ' đ x ' . $total_day_use.' ngày' . ' = ' . CUtils::formatPrice(($serviceParkingLevel->price/$total_day_in_month) * $total_day_use) . " đ\n";
            $description_en .= $serviceParkingLevel->name_en . ': ' . CUtils::formatPrice($serviceParkingLevel->price/$total_day_in_month) . ' đ x ' . $total_day_use.' day' . ' = ' . CUtils::formatPrice(($serviceParkingLevel->price/$total_day_in_month) * $total_day_use) . " đ\n";
            $total_money += ($serviceParkingLevel->price/$total_day_in_month) * $total_day_use;
            $json_desc['muc_phi_le'] = [
                'text' => $serviceParkingLevel->name . ': ' . CUtils::formatPrice($serviceParkingLevel->price/$total_day_in_month) . ' đ x ' . $total_day_use.' ngày' . ' = ' . CUtils::formatPrice(($serviceParkingLevel->price/$total_day_in_month) * $total_day_use) . " đ",
                'text_en' => $serviceParkingLevel->name_en . ': ' . CUtils::formatPrice($serviceParkingLevel->price/$total_day_in_month) . ' đ x ' . $total_day_use.' day' . ' = ' . CUtils::formatPrice(($serviceParkingLevel->price/$total_day_in_month) * $total_day_use) . " đ",
                'price' => $serviceParkingLevel->price,
                'total_day_in_month' => $total_day_in_month,
                'total_day_use' => $total_day_use,
            ];
        }
        $x = $x + $is_le_last;
        if($count_month-$x > 0){
            $description .= $serviceParkingLevel->name . ': ' . CUtils::formatPrice($serviceParkingLevel->price) . ' đ x ' . ($count_month-$x).' tháng' . ' = ' . CUtils::formatPrice($serviceParkingLevel->price * ($count_month-$x)) . " đ\n";
            $description_en .= $serviceParkingLevel->name_en . ': ' . CUtils::formatPrice($serviceParkingLevel->price) . ' đ x ' . ($count_month-$x).' month' . ' = ' . CUtils::formatPrice($serviceParkingLevel->price * ($count_month-$x)) . " đ\n";
            $total_money += $serviceParkingLevel->price * ($count_month-$x);
            $json_desc['muc_phi_thang'] = [
                'text' => $serviceParkingLevel->name . ': ' . CUtils::formatPrice($serviceParkingLevel->price) . ' đ x ' . ($count_month-$x).' tháng' . ' = ' . CUtils::formatPrice($serviceParkingLevel->price * ($count_month-$x)) . " đ",
                'text_en' => $serviceParkingLevel->name_en . ': ' . CUtils::formatPrice($serviceParkingLevel->price) . ' đ x ' . ($count_month-$x).' tháng' . ' = ' . CUtils::formatPrice($serviceParkingLevel->price * ($count_month-$x)) . " đ",
                'price' => $serviceParkingLevel->price,
                'count_month' => $count_month-$x,
            ];
        }

        if($is_le_last == 1){
            $current_last_month_cancel = date('Y-m-01 00:00:00', strtotime('+1 month', strtotime(date('Y-m-01', strtotime($cancel_date)))));
            $diff_day_in_month = date_diff(date_create(date('Y-m-01', strtotime($cancel_date))),date_create($current_last_month_cancel));
            $total_day_in_month = (int)$diff_day_in_month->format("%R%a");

            $diff_day_use =date_diff(date_create(date('Y-m-01', strtotime($cancel_date))),date_create(date('Y-m-d', strtotime($cancel_date))));
            $total_day_use =  (int)$diff_day_use->format("%R%a") + 1;

            $description .= $serviceParkingLevel->name . ': ' . CUtils::formatPrice($serviceParkingLevel->price/$total_day_in_month) . ' đ x ' . $total_day_use.' ngày' . ' = ' . CUtils::formatPrice(($serviceParkingLevel->price/$total_day_in_month) * $total_day_use) . " đ\n";
            $description_en .= $serviceParkingLevel->name_en . ': ' . CUtils::formatPrice($serviceParkingLevel->price/$total_day_in_month) . ' đ x ' . $total_day_use.' day' . ' = ' . CUtils::formatPrice(($serviceParkingLevel->price/$total_day_in_month) * $total_day_use) . " đ\n";
            $total_money += ($serviceParkingLevel->price/$total_day_in_month) * $total_day_use;
            $json_desc['muc_phi_le'] = [
                'text' => $serviceParkingLevel->name . ': ' . CUtils::formatPrice($serviceParkingLevel->price/$total_day_in_month) . ' đ x ' . $total_day_use.' ngày' . ' = ' . CUtils::formatPrice(($serviceParkingLevel->price/$total_day_in_month) * $total_day_use) . " đ",
                'text_en' => $serviceParkingLevel->name_en . ': ' . CUtils::formatPrice($serviceParkingLevel->price/$total_day_in_month) . ' đ x ' . $total_day_use.' day' . ' = ' . CUtils::formatPrice(($serviceParkingLevel->price/$total_day_in_month) * $total_day_use) . " đ",
                'price' => $serviceParkingLevel->price,
                'total_day_in_month' => $total_day_in_month,
                'total_day_use' => $total_day_use,
            ];
        }

        $serviceVehicleConfig = ServiceVehicleConfig::findOne(['building_cluster_id' => $serviceParkingLevel->building_cluster_id, 'service_map_management_id' => $serviceParkingLevel->service_map_management_id]);
        //tinh them % phu phi, thue, phi vat, phi bvmt
        $percent = [];
        $tax_money = 0;
        if(!empty($serviceVehicleConfig->percent)){
            $tax_money = $total_money * ($serviceVehicleConfig->percent /100);
            $percent['tax_percent'] = $serviceVehicleConfig->percent;
            $description .= "Phí khác: " . CUtils::formatPrice($tax_money) ." đ\n";
            $description_en .= "Other fees: " . CUtils::formatPrice($tax_money) ." đ\n";
        }
        $percent['tax_money'] = $tax_money;

        $vat_money = 0;
        if(!empty($serviceVehicleConfig->vat_percent)){
            $vat_money = $total_money * ($serviceVehicleConfig->vat_percent /100);
            $percent['vat_percent'] = $serviceVehicleConfig->vat_percent;
            $description .= "Thuế VAT: " . CUtils::formatPrice($vat_money) ." đ\n";
            $description_en .= "VAT: " . CUtils::formatPrice($vat_money) ." đ\n";
        }
        $percent['vat_money'] = $vat_money;

//        $tax_percent = 0;
//        if(!empty($serviceWaterConfig->tax_percent)){
//            $tax_percent = $total_money * ($serviceWaterConfig->tax_percent /100);
//        }

        $environ_money = 0;
        if(!empty($serviceVehicleConfig->environ_percent)){
            $environ_money = $total_money * ($serviceVehicleConfig->environ_percent /100);
            $percent['environ_percent'] = $serviceVehicleConfig->environ_percent;
            $description .= "Phí BVMT: " . CUtils::formatPrice($environ_money) ." đ\n";
            $description_en .= "Environmental protection fees: " . CUtils::formatPrice($environ_money) ." đ\n";
        }
        $percent['environ_money'] = $environ_money;

        $dateRes['end_time'] = strtotime('-1 day', strtotime($current_last_date));
        $dateRes['description'] = $description;
        $dateRes['description_en'] = $description_en;
        $percent['total_money_net'] = round($total_money); // chưa cộng các phí
        $dateRes['total_money'] = round(($total_money + $tax_money + $vat_money + $environ_money)); // đã cộng các phí
        $json_desc['percent'] = $percent;
        $dateRes['json_desc'] = $json_desc;
        return $dateRes;
    }

    public function setParams($edit = false)
    {
        $start_time = time();
        $cancel_date = null;
        if($this->serviceManagementVehicle){
            if($edit == true){
                if(!empty($this->serviceManagementVehicle->end_date)){
                    $start_time = $this->serviceManagementVehicle->end_date;
                }
            }else{
                if(!empty($this->serviceManagementVehicle->tmp_end_date)){
                    $start_time = $this->serviceManagementVehicle->tmp_end_date;
                }
            }
            $cancel_date = $this->serviceManagementVehicle->cancel_date;
        }

//        $serviceParkingFee = ServiceParkingFee::find()->where(['building_cluster_id' => $this->building_cluster_id, 'apartment_id' => $this->apartment_id, 'service_management_vehicle_id' => $this->service_management_vehicle_id])->orderBy(['end_time' => SORT_DESC])->one();
//        if(!empty($serviceParkingFee)){
//            $start_time = $serviceParkingFee->end_time;
//        }else{
//            if($this->serviceManagementVehicle){
//                $start_time = $this->serviceManagementVehicle->start_date;
//            }else{
//                $serviceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $this->service_management_vehicle_id]);
//                $start_time = $serviceManagementVehicle->start_date;
//            }
//        }
        $start_time = strtotime('+1 day', $start_time);
        $this->fee_of_month = $start_time;
        $this->start_time = $start_time;
        $res = self::getCharge($this->start_time, $this->service_parking_level_id, $this->count_month, $cancel_date);
        $this->total_money = $res['total_money'];
        $this->end_time = $res['end_time'];
        $this->description = $res['description'];
        $this->description_en = $res['description_en'];
        $this->json_desc = json_encode($res['json_desc']);
    }

    public function updateEndTime(){
        $serviceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $this->service_management_vehicle_id]);
        if(!empty($serviceManagementVehicle)){
            $serviceParkingFeeNew = ServiceParkingFee::find()->where(['service_management_vehicle_id' => $this->service_management_vehicle_id, 'apartment_id' => $this->apartment_id])->orderBy(['end_time' => SORT_DESC])->one();
            $end_time = $this->end_time;
            if(!empty($serviceParkingFeeNew)){
                $end_time = $serviceParkingFeeNew->end_time;
            }
            if($serviceManagementVehicle->tmp_end_date < $end_time){
                $serviceManagementVehicle->tmp_end_date = $end_time;
                if(!$serviceManagementVehicle->save()){
                    Yii::error($serviceManagementVehicle->errors);
                }
            }
        }
    }

    public function resetInfo()
    {
        $serviceManagementVehicle = ServiceManagementVehicle::findOne(['id' => $this->service_management_vehicle_id]);
        if($this->status === ServiceParkingFee::STATUS_ACTIVE){
            if(!empty($serviceManagementVehicle)){
                if($serviceManagementVehicle->tmp_end_date == $this->end_time){
                    $serviceManagementVehicle->tmp_end_date = strtotime('-1 day', $this->start_time);
                    if($serviceManagementVehicle->end_date > $serviceManagementVehicle->tmp_end_date){
                        $serviceManagementVehicle->end_date = $serviceManagementVehicle->tmp_end_date;
                    };
                    if(!$serviceManagementVehicle->save()){
                        Yii::error($serviceManagementVehicle->errors);
                        return false;
                    }
                    if(!$this->delete()){
                        Yii::error($this->errors);
                        return false;
                    }
                    return true;
                }
            }
            return false;
        }else{
            if(!empty($serviceManagementVehicle)){
//                $serviceManagementVehicle->tmp_end_date = $serviceManagementVehicle->end_date;
                $serviceManagementVehicle->tmp_end_date = strtotime('-1 day', $this->start_time);
                if($serviceManagementVehicle->end_date > $serviceManagementVehicle->tmp_end_date){
                    $serviceManagementVehicle->end_date = $serviceManagementVehicle->tmp_end_date;
                };
                if(!$serviceManagementVehicle->save()){
                    Yii::error($serviceManagementVehicle->errors);
                    return false;
                }
            }
            if(!$this->delete()){
                Yii::error($this->errors);
                return false;
            }
            return true;
        }

    }
}
