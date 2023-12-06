<?php

namespace common\models;

use common\helpers\CUtils;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_building_fee".
 *
 * @property int $id
 * @property int $service_map_management_id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property int $count_month số tháng gửi tiếp theo
 * @property int $start_time
 * @property int $end_time
 * @property int $service_building_config_id
 * @property int $total_money
 * @property int $status 0 - chưa duyệt, 1 - đã duyệt
 * @property string $description
 * @property string $description_en
 * @property string $json_desc
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $service_payment_fee_id
 * @property int $is_created_fee
 * @property int $fee_of_month
 *
 * @property Apartment $apartment
 * @property BuildingCluster $buildingCluster
 * @property ServiceBuildingConfig $serviceBuildingConfig
 * @property ServiceMapManagement $serviceMapManagement
 * @property ManagementUser $managementUser
 * @property ServicePaymentFee $servicePaymentFee
 */
class ServiceBuildingFee extends \yii\db\ActiveRecord
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
        return 'service_building_fee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_map_management_id', 'building_cluster_id', 'apartment_id', 'service_building_config_id'], 'required'],
            [['fee_of_month', 'is_created_fee', 'service_payment_fee_id', 'service_map_management_id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'count_month', 'start_time', 'end_time', 'service_building_config_id', 'total_money', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
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
            'service_building_config_id' => Yii::t('common', 'Service Building Config ID'),
            'total_money' => Yii::t('common', 'Total Money'),
            'status' => Yii::t('common', 'Status'),
            'service_payment_fee_id' => Yii::t('common', 'service Payment Fee Id'),
            'is_created_fee' => Yii::t('common', 'Is Created Fee'),
            'description' => Yii::t('common', 'Description'),
            'json_desc' => Yii::t('common', 'Json Desc'),
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
    public function getServiceBuildingConfig()
    {
        return $this->hasOne(ServiceBuildingConfig::className(), ['id' => 'service_building_config_id']);
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
    public function getServiceMapManagement()
    {
        return $this->hasOne(ServiceMapManagement::className(), ['id' => 'service_map_management_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagementUser()
    {
        return $this->hasOne(ManagementUser::className(), ['id' => 'updated_by']);
    }

    public static function getCharge($start_time, $building_cluster_id, $count_month, $capacity)
    {
        $start_time = strtotime(date('Y-m-d 00:00:00', $start_time));

        $current_day = (int)date('d', $start_time);
        $current_month = date('m', $start_time);
        $current_year = (int)date('Y', $start_time);
        $current_year_in = (int)date('Y', time());
        $total_month = $current_month;
//        if($current_year !== $current_year_in){
        $total_month .= '/'.$current_year;
//        }
        $j = (int)$current_month;
        $t = 0;
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
            if(($current_year + $t) !== $current_year_in){
                $m .= '/'.($current_year+$t);
            }else{
                $m .= '/'.$current_year;
            }
            $total_month .= ', ' . $m;
        }
        $dateRes = [
            'total_month' => $total_month,
            'description' => '',
            'description_en' => '',
            'total_money' => 0,
            'end_time' => 0,
        ];
        $description = "Phí dịch vụ tháng $total_month:\n";
        $description_en = "Month service fees $total_month:\n";
        $json_desc['dien_tich'] = [
            'text' => $description,
            'text_en' => $description_en,
            'capacity' => $capacity,
            'unit' => 'm2'
        ];
        $total_money = 0;
        $serviceBuildingConfig = ServiceBuildingConfig::findOne(['building_cluster_id' => $building_cluster_id]);
        $x = 0;
        if($current_day > 1){
            $x = 1;
            $diff_day_in_month = date_diff(date_create(date('Y-m-01', $start_time)),date_create(date('Y-m-01', strtotime('+1 month', strtotime(date('Y-m-01', $start_time))))));
            $total_day_in_month = (int)$diff_day_in_month->format("%R%a");

            $diff_day_use =date_diff(date_create(date('Y-m-d', $start_time)),date_create(date('Y-m-01', strtotime('+1 month', strtotime(date('Y-m-01', $start_time))))));
            $total_day_use =  (int)$diff_day_use->format("%R%a");
            if($serviceBuildingConfig->unit == ServiceBuildingConfig::UNIT_M2){
                $description .= 'Mức phí lẻ: ' . CUtils::formatPrice($serviceBuildingConfig->price/$total_day_in_month) . 'đ x ' . $capacity . 'm2 x ' . $total_day_use.'ngày' . ' = ' . CUtils::formatPrice(($serviceBuildingConfig->price/$total_day_in_month) * $capacity * $total_day_use) . " đ\n";
                $description_en .= 'Odd fee: ' . CUtils::formatPrice($serviceBuildingConfig->price/$total_day_in_month) . 'đ x ' . $capacity . 'm2 x ' . $total_day_use.'day' . ' = ' . CUtils::formatPrice(($serviceBuildingConfig->price/$total_day_in_month) * $capacity * $total_day_use) . " đ\n";
                $total_money += ($serviceBuildingConfig->price/$total_day_in_month) * $capacity * $total_day_use;
                $json_desc['muc_phi_le'] = [
                    'text' => 'Mức phí lẻ: ' . CUtils::formatPrice($serviceBuildingConfig->price/$total_day_in_month) . 'đ x ' . $capacity . 'm2 x ' . $total_day_use.'ngày' . ' = ' . CUtils::formatPrice(($serviceBuildingConfig->price/$total_day_in_month) * $capacity * $total_day_use) . " đ",
                    'text_en' => 'Odd fee: ' . CUtils::formatPrice($serviceBuildingConfig->price/$total_day_in_month) . 'đ x ' . $capacity . 'm2 x ' . $total_day_use.'day' . ' = ' . CUtils::formatPrice(($serviceBuildingConfig->price/$total_day_in_month) * $capacity * $total_day_use) . " đ",
                    'price' => $serviceBuildingConfig->price,
                    'total_day_in_month' => $total_day_in_month,
                    'total_day_use' => $total_day_use,
                    'capacity' => $capacity,
                    'unit' => 'm2/ngày'
                ];
            }else{
                $description .= 'Mức phí lẻ: ' . CUtils::formatPrice($serviceBuildingConfig->price/$total_day_in_month) . 'đ x ' . $total_day_use.'ngày' . ' = ' . CUtils::formatPrice(($serviceBuildingConfig->price/$total_day_in_month) * $total_day_use) . " đ\n";
                $description_en .= 'Odd fee: ' . CUtils::formatPrice($serviceBuildingConfig->price/$total_day_in_month) . 'đ x ' . $total_day_use.'day' . ' = ' . CUtils::formatPrice(($serviceBuildingConfig->price/$total_day_in_month) * $total_day_use) . " đ\n";
                $total_money += ($serviceBuildingConfig->price/$total_day_in_month) * $total_day_use;
                $json_desc['muc_phi_le'] = [
                    'text' => 'Mức phí lẻ: ' . CUtils::formatPrice($serviceBuildingConfig->price/$total_day_in_month) . 'đ x ' . $total_day_use.'ngày' . ' = ' . CUtils::formatPrice(($serviceBuildingConfig->price/$total_day_in_month) * $total_day_use) . " đ",
                    'text_en' => 'Odd fee: ' . CUtils::formatPrice($serviceBuildingConfig->price/$total_day_in_month) . 'đ x ' . $total_day_use.'day' . ' = ' . CUtils::formatPrice(($serviceBuildingConfig->price/$total_day_in_month) * $total_day_use) . " đ",
                    'price' => $serviceBuildingConfig->price,
                    'total_day_in_month' => $total_day_in_month,
                    'total_day_use' => $total_day_use,
                    'unit' => 'Căn hộ/ngày'
                ];
            }
        }
        if($count_month-$x > 0){
            if($serviceBuildingConfig->unit == ServiceBuildingConfig::UNIT_M2){
                $description .= 'Mức phí tháng: ' . CUtils::formatPrice($serviceBuildingConfig->price) . 'đ x ' . $capacity . 'm2 x ' . ($count_month-$x).'tháng' . ' = ' . CUtils::formatPrice($serviceBuildingConfig->price * $capacity * ($count_month-$x)) . " đ\n";
                $description_en .= 'Month fee: ' . CUtils::formatPrice($serviceBuildingConfig->price) . 'đ x ' . $capacity . 'm2 x ' . ($count_month-$x).'month' . ' = ' . CUtils::formatPrice($serviceBuildingConfig->price * $capacity * ($count_month-$x)) . " đ\n";
                $total_money += $serviceBuildingConfig->price * $capacity * ($count_month-$x);
                $json_desc['muc_phi_chan'] = [
                    'text' => 'Mức phí tháng: ' . CUtils::formatPrice($serviceBuildingConfig->price) . 'đ x ' . $capacity . 'm2 x ' . ($count_month-$x).'tháng' . ' = ' . CUtils::formatPrice($serviceBuildingConfig->price * $capacity * ($count_month-$x)) . " đ",
                    'text_en' => 'Month fee: ' . CUtils::formatPrice($serviceBuildingConfig->price) . 'đ x ' . $capacity . 'm2 x ' . ($count_month-$x).'month' . ' = ' . CUtils::formatPrice($serviceBuildingConfig->price * $capacity * ($count_month-$x)) . " đ",
                    'price' => $serviceBuildingConfig->price,
                    'month_cycle' => $serviceBuildingConfig->month_cycle - $x,
                    'capacity' => $capacity,
                    'unit' => 'm2/tháng'
                ];
            }else{
                $description .= 'Mức phí tháng: ' . CUtils::formatPrice($serviceBuildingConfig->price) . 'đ x ' . ($count_month-$x).'tháng' . ' = ' . CUtils::formatPrice($serviceBuildingConfig->price * ($count_month-$x)) . " đ\n";
                $description_en .= 'Month fee: ' . CUtils::formatPrice($serviceBuildingConfig->price) . 'đ x ' . ($count_month-$x).'month' . ' = ' . CUtils::formatPrice($serviceBuildingConfig->price * ($count_month-$x)) . " đ\n";
                $total_money += $serviceBuildingConfig->price * ($count_month-$x);
                $json_desc['muc_phi_chan'] = [
                    'text' => 'Mức phí tháng: ' . CUtils::formatPrice($serviceBuildingConfig->price) . 'đ x ' . ($count_month-$x).'tháng' . ' = ' . CUtils::formatPrice($serviceBuildingConfig->price * ($count_month-$x)) . " đ",
                    'text_en' => 'Month fee: ' . CUtils::formatPrice($serviceBuildingConfig->price) . 'đ x ' . ($count_month-$x).'month' . ' = ' . CUtils::formatPrice($serviceBuildingConfig->price * ($count_month-$x)) . " đ",
                    'price' => $serviceBuildingConfig->price,
                    'month_cycle' => $serviceBuildingConfig->month_cycle - $x,
                    'unit' => 'Căn hộ/tháng'
                ];
            }
        }
        //tinh them % phu phi, thue, phi vat, phi bvmt
        $percent = [];
        $tax_money = 0;
        if(!empty($serviceBuildingConfig->percent)){
            $tax_money = $total_money * ($serviceBuildingConfig->percent /100);
            $percent['tax_percent'] = $serviceBuildingConfig->percent;
            $description .= "Phí khác: " . CUtils::formatPrice($tax_money) ." đ\n";
            $description_en .= "Other fees: " . CUtils::formatPrice($tax_money) ." đ\n";
        }
        $percent['tax_money'] = $tax_money;

        $vat_money = 0;
        if(!empty($serviceBuildingConfig->vat_percent)){
            $vat_money = $total_money * ($serviceBuildingConfig->vat_percent /100);
            $percent['vat_percent'] = $serviceBuildingConfig->vat_percent;
            $description .= "Thuế VAT: " . CUtils::formatPrice($vat_money) ." đ\n";
            $description_en .= "VAT: " . CUtils::formatPrice($vat_money) ." đ\n";
        }
        $percent['vat_money'] = $vat_money;

//        $tax_percent = 0;
//        if(!empty($serviceBuildingConfig->tax_percent)){
//            $tax_percent = $total_money * ($serviceBuildingConfig->tax_percent /100);
//        }

        $environ_money = 0;
        if(!empty($serviceBuildingConfig->environ_percent)){
            $environ_money = $total_money * ($serviceBuildingConfig->environ_percent /100);
            $percent['environ_percent'] = $serviceBuildingConfig->environ_percent;
            $description .= "Phí BVMT: " . CUtils::formatPrice($environ_money) ." đ\n";
            $description_en .= "Environmental protection fees: " . CUtils::formatPrice($environ_money) ." đ\n";
        }
        $percent['environ_money'] = $environ_money;

        $dateRes['end_time'] = strtotime('-1 day', strtotime('+1 month', strtotime(date('Y-m-01', $start_time))));
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
        $apartment = Apartment::find()->where(['building_cluster_id' => $this->building_cluster_id, 'id' => $this->apartment_id, 'is_deleted' => Apartment::NOT_DELETED])->one();
        $serviceBuildingInfo = ServiceBuildingInfo::findOne(['building_cluster_id' => $this->building_cluster_id, 'apartment_id' => $this->apartment_id]);
        if (!empty($serviceBuildingInfo)) {
            if($edit == true){
                $start_time = $serviceBuildingInfo->end_date;
            }else{
                $start_time = $serviceBuildingInfo->tmp_end_date;
            }
        }
        if(empty($start_time)){
            $start_time = $apartment->date_received;
        }
        $start_time = strtotime('+1 day', $start_time);
        $this->fee_of_month = $start_time;
        $this->start_time = $start_time;
        $res = self::getCharge($this->start_time, $this->building_cluster_id, $this->count_month, $apartment->capacity);
        $this->total_money = $res['total_money'];
        $this->end_time = $res['end_time'];
        $this->description = $res['description'];
        $this->description_en = $res['description_en'];
        $this->json_desc = json_encode($res['json_desc']);
    }

    public function updateEndTime(){
        $serviceBuildingInfo = ServiceBuildingInfo::findOne(['building_cluster_id' => $this->building_cluster_id, 'apartment_id' => $this->apartment_id]);
        if(!empty($serviceBuildingInfo)){
            $serviceBuildingFeeNew = ServiceBuildingFee::find()->where(['building_cluster_id' => $this->building_cluster_id, 'apartment_id' => $this->apartment_id])->orderBy(['end_time' => SORT_DESC])->one();
            $end_time = $this->end_time;
            if(!empty($serviceBuildingFeeNew)){
                $end_time = $serviceBuildingFeeNew->end_time;
            }
            if($serviceBuildingInfo->tmp_end_date < $end_time){
                $serviceBuildingInfo->tmp_end_date = $end_time;
                if(!$serviceBuildingInfo->save()){
                    Yii::error($serviceBuildingInfo->errors);
                }
            }
        }
    }

    public function resetInfo()
    {
        $serviceBuildingInfo = ServiceBuildingInfo::findOne(['building_cluster_id' => $this->building_cluster_id, 'apartment_id' => $this->apartment_id, 'service_map_management_id' => $this->service_map_management_id]);
        if($this->status === ServiceBuildingFee::STATUS_ACTIVE){
            if(!empty($serviceBuildingInfo)){
                if($serviceBuildingInfo->tmp_end_date === $this->end_time){
                    $serviceBuildingInfo->tmp_end_date = strtotime('-1 day', $this->start_time);
                    if($serviceBuildingInfo->end_date > $serviceBuildingInfo->tmp_end_date){
                        $serviceBuildingInfo->end_date = $serviceBuildingInfo->tmp_end_date;
                    };
                    if(!$serviceBuildingInfo->save()){
                        Yii::error($serviceBuildingInfo->errors);
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
            if(!empty($serviceBuildingInfo)){
                $serviceBuildingInfo->tmp_end_date = strtotime('-1 day', $this->start_time);
                if(!$serviceBuildingInfo->save()){
                    Yii::error($serviceBuildingInfo->errors);
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
