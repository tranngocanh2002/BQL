<?php

namespace common\models;

use common\helpers\CUtils;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "service_electric_fee".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property int $service_map_management_id
 * @property int $start_index chỉ số đầu
 * @property int $end_index chỉ số cuối
 * @property int $total_index tổng chỉ số sử dụng
 * @property int $total_money tổng tiền
 * @property int $start_time thời gian bắt đầu
 * @property int $lock_time thời gian chốt
 * @property int $fee_of_month phí của tháng
 * @property string $description
 * @property string $description_en
 * @property string $json_desc
 * @property int $status 0 - chưa duyệt, 1 - đã duyệt
 * @property int $is_created_fee 0 - chưa tạo phí thanh toán, 1 - đã tạo phí thanh toán => không được sửa
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $service_payment_fee_id
 *
 * @property Apartment $apartment
 * @property ServiceMapManagement $serviceMapManagement
 * @property ManagementUser $managementUser
 * @property ServicePaymentFee $servicePaymentFee
 */
class ServiceElectricFee extends \yii\db\ActiveRecord
{
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const IS_UNCREATED_FEE = 0;
    const IS_CREATED_FEE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_electric_fee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_payment_fee_id', 'start_time', 'building_cluster_id', 'building_area_id', 'apartment_id', 'service_map_management_id', 'start_index', 'end_index', 'total_index', 'total_money', 'lock_time', 'status', 'is_created_fee', 'fee_of_month', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
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
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'service_map_management_id' => Yii::t('common', 'Service Map Management ID'),
            'start_index' => Yii::t('common', 'Start Index'),
            'end_index' => Yii::t('common', 'End Index'),
            'total_index' => Yii::t('common', 'Total Index'),
            'total_money' => Yii::t('common', 'Total Money'),
            'start_time' => Yii::t('common', 'Start Time'),
            'lock_time' => Yii::t('common', 'Lock Time'),
            'fee_of_month' => Yii::t('common', 'Fee Of Month'),
            'description' => Yii::t('common', 'Description'),
            'json_desc' => Yii::t('common', 'Json Desc'),
            'status' => Yii::t('common', 'Status'),
            'is_created_fee' => Yii::t('common', 'Is Created Fee'),
            'service_payment_fee_id' => Yii::t('common', 'Service Payment Fee Id'),
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
            ]
        ];
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
    public function getServiceMapManagement()
    {
        return $this->hasOne(ServiceMapManagement::className(), ['id' => 'service_map_management_id']);
    }

    public function getTotalIndex()
    {
        $this->total_index = $this->end_index - $this->start_index;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagementUser()
    {
        return $this->hasOne(ManagementUser::className(), ['id' => 'updated_by']);
    }

    public static function getCharge($building_cluster_id, $service_map_management_id, $end_index, $start_index, $lock_time, $apartment_id)
    {
        $type = ServiceElectricConfig::TYPE_APARTMENT;
        $coefficient = 1; // default tính theo căn hộ nên hệ số = 1
        //kiểm tra loại tính dịch vụ
        $ServiceElectricConfig = ServiceElectricConfig::findOne(['building_cluster_id' => $building_cluster_id, 'service_map_management_id' => $service_map_management_id]);
        if (!empty($ServiceElectricConfig)) {
            //nếu tính theo đầu người thì phải lấy ra tổng số thành viên trong căn hộ và sét thành hệ số
            if ($ServiceElectricConfig->type == ServiceElectricConfig::TYPE_RESIDENT) {
                $type = ServiceElectricConfig::TYPE_RESIDENT;
                $apartment = Apartment::findOne(['id' => $apartment_id]);
                $coefficient = $apartment->total_members;
            }
        }

        $total_index = $end_index - $start_index;
        $dateRes = [
            'total_index' => $total_index,
            'description' => '',
            'description_en' => '',
            'total_money' => 0,
        ];
        $serviceElectricLevels = ServiceElectricLevel::find()->where(['building_cluster_id' => $building_cluster_id, 'service_map_management_id' => $service_map_management_id])->orderBy(['from_level' => SORT_ASC])->all();
        $month = date('m', $lock_time);
        $description = "";
        $description_en = "";
        if($type == ServiceElectricConfig::TYPE_RESIDENT){
            $description = "Số người theo định mức: $coefficient người\n";
            $description_en = "Number of people by level: $coefficient people\n";
        }
        $json_desc['month'] = [
            'text' => $description,
            'text_en' => $description_en,
            'total_index' => $total_index,
            'start_index' => $start_index,
            'end_index' => $end_index,
        ];
        $json_desc['dm'] = [];
        $json_desc['dm_arr'] = [];
        $total_money = 0;
        $i = 1;
        $to_level_old = 0;
        foreach ($serviceElectricLevels as $serviceElectricLevel) {
            // 0|from_level -> to_level
            $from_level = $serviceElectricLevel->from_level;
            if ($type == ServiceElectricConfig::TYPE_RESIDENT) {
                $from_level = ($to_level_old * $coefficient) + 1;
            }
            $to_level_old = $serviceElectricLevel->to_level;
            $to_level = $serviceElectricLevel->to_level * $coefficient;

            if ($from_level <= 0) {
                if ($total_index < $to_level) {
                    $total_money += $total_index * $serviceElectricLevel->price;
                    $description .= "Định mức $i: " . $total_index . " x " . CUtils::formatPrice($serviceElectricLevel->price) . " đ = " . CUtils::formatPrice($total_index * $serviceElectricLevel->price) . " đ\n";
                    $description_en .= "Level $i: " . $total_index . " x " . CUtils::formatPrice($serviceElectricLevel->price) . " đ = " . CUtils::formatPrice($total_index * $serviceElectricLevel->price) . " đ\n";
                    $json_desc['dm'][$i] = "Định mức $i: " . $total_index . " x " . CUtils::formatPrice($serviceElectricLevel->price) . " đ = " . CUtils::formatPrice($total_index * $serviceElectricLevel->price)." đ";
                    $json_desc['dm_arr'][$i] = [
                        'text' => "Định mức $i ",
                        'text_en' => "Level $i ",
                        'index' => $total_index,
                        'price' => $serviceElectricLevel->price,
                        'total_price' => $total_index * $serviceElectricLevel->price
                    ];
                    break;
                } else {
                    $total_index_new = $to_level;
                    $total_money += $total_index_new * $serviceElectricLevel->price;
                    $description .= "Định mức $i: " . $total_index_new . " x " . CUtils::formatPrice($serviceElectricLevel->price) . " đ = " . CUtils::formatPrice($total_index_new * $serviceElectricLevel->price) . " đ\n";
                    $description_en .= "Level $i: " . $total_index_new . " x " . CUtils::formatPrice($serviceElectricLevel->price) . " đ = " . CUtils::formatPrice($total_index_new * $serviceElectricLevel->price) . " đ\n";
                    $json_desc['dm'][$i] = "Định mức $i: " . $total_index_new . " x " . CUtils::formatPrice($serviceElectricLevel->price) . " đ = " . CUtils::formatPrice($total_index_new * $serviceElectricLevel->price)." đ";
                    $json_desc['dm_arr'][$i] = [
                        'text' => "Định mức $i ",
                        'text_en' => "Level $i ",
                        'index' => $total_index_new,
                        'price' => $serviceElectricLevel->price,
                        'total_price' => $total_index_new * $serviceElectricLevel->price
                    ];
                }
            } else {
                if ($total_index < $to_level) {
                    $total_index_new = $total_index - $from_level + 1;
                    if ($total_index_new == 0) {
                        break;
                    }
                    $total_money += $total_index_new * $serviceElectricLevel->price;
                    $description .= "Định mức $i: " . $total_index_new . " x " . CUtils::formatPrice($serviceElectricLevel->price) . " đ = " . CUtils::formatPrice($total_index_new * $serviceElectricLevel->price) . " đ\n";
                    $description_en .= "Level $i: " . $total_index_new . " x " . CUtils::formatPrice($serviceElectricLevel->price) . " đ = " . CUtils::formatPrice($total_index_new * $serviceElectricLevel->price) . " đ\n";
                    $json_desc['dm'][$i] = "Định mức $i: " . $total_index_new . " x " . CUtils::formatPrice($serviceElectricLevel->price) . " đ = " . CUtils::formatPrice($total_index_new * $serviceElectricLevel->price)." đ";
                    $json_desc['dm_arr'][$i] = [
                        'text' => "Định mức $i ",
                        'text_en' => "Level $i ",
                        'index' => $total_index_new,
                        'price' => $serviceElectricLevel->price,
                        'total_price' => $total_index_new * $serviceElectricLevel->price
                    ];
                    break;
                } else {
                    $total_index_new = $to_level - $from_level + 1;
                    if ($total_index_new == 0) {
                        break;
                    }
                    $total_money += $total_index_new * $serviceElectricLevel->price;
                    $description .= "Định mức $i: " . $total_index_new . " x " . CUtils::formatPrice($serviceElectricLevel->price) . " đ = " . CUtils::formatPrice($total_index_new * $serviceElectricLevel->price) . " đ\n";
                    $description_en .= "Level $i: " . $total_index_new . " x " . CUtils::formatPrice($serviceElectricLevel->price) . " đ = " . CUtils::formatPrice($total_index_new * $serviceElectricLevel->price) . " đ\n";
                    $json_desc['dm'][$i] = "Định mức $i: " . $total_index_new . " x " . CUtils::formatPrice($serviceElectricLevel->price) . " đ = " . CUtils::formatPrice($total_index_new * $serviceElectricLevel->price)." đ";
                    $json_desc['dm_arr'][$i] = [
                        'text' => "Định mức $i ",
                        'text_en' => "Level $i ",
                        'index' => $total_index_new,
                        'price' => $serviceElectricLevel->price,
                        'total_price' => $total_index_new * $serviceElectricLevel->price
                    ];
                }
            }
            $i++;
        }
        //tinh them % phu phi, thue, phi vat, phi bvmt
        $percent = [];
        $tax_money = 0;
        if(!empty($ServiceElectricConfig->percent)){
            $tax_money = $total_money * ($ServiceElectricConfig->percent /100);
            $percent['tax_percent'] = $ServiceElectricConfig->percent;
            $description .= "Phí khác: " . CUtils::formatPrice($tax_money) ." đ\n";
            $description_en .= "Other fees: " . CUtils::formatPrice($tax_money) ." đ\n";
        }
        $percent['tax_money'] = $tax_money;

        $vat_money = 0;
        if(!empty($ServiceElectricConfig->vat_percent)){
            $vat_money = $total_money * ($ServiceElectricConfig->vat_percent /100);
            $percent['vat_percent'] = $ServiceElectricConfig->vat_percent;
            $description .= "Thuế VAT: " . CUtils::formatPrice($vat_money) ." đ\n";
            $description_en .= "VAT: " . CUtils::formatPrice($vat_money) ." đ\n";
        }
        $percent['vat_money'] = $vat_money;

//        $tax_percent = 0;
//        if(!empty($ServiceElectricConfig->tax_percent)){
//            $tax_percent = $total_money * ($ServiceElectricConfig->tax_percent /100);
//        }

        $environ_money = 0;
        if(!empty($ServiceElectricConfig->environ_percent)){
            $environ_money = $total_money * ($ServiceElectricConfig->environ_percent /100);
            $percent['environ_percent'] = $ServiceElectricConfig->environ_percent;
            $description .= "Phí BVMT: " . CUtils::formatPrice($environ_money) ." đ\n";
            $description_en .= "Environmental protection fees: " . CUtils::formatPrice($environ_money) ." đ\n";
        }
        $percent['environ_money'] = $environ_money;

        $dateRes['description'] = $description;
        $dateRes['description_en'] = $description_en;
//        $dateRes['total_money'] = round($total_money/1000)*1000;
        $percent['total_money_net'] = round($total_money); // chưa cộng các phí
        $dateRes['total_money'] = round(($total_money + $tax_money + $vat_money + $environ_money)); // đã cộng các phí
        $json_desc['percent'] = $percent;
        $dateRes['json_desc'] = $json_desc;
        return $dateRes;
    }

    public function getTotalMoney()
    {
        $res = self::getCharge($this->building_cluster_id, $this->service_map_management_id, $this->end_index, $this->start_index, $this->lock_time, $this->apartment_id);
        $this->total_money = $res['total_money'];
        $this->description = $res['description'];
        $this->description_en = $res['description_en'];
        $this->json_desc = json_encode($res['json_desc']);
    }

    public function resetInfo($check_fee = false)
    {
        //check phi thang sau
        $ServiceElectricFeeCheck = ServiceElectricFee::find()->where(['>', 'lock_time', $this->lock_time])
            ->andWhere(['<>', 'id', $this->id])
            ->andWhere(['apartment_id' => $this->apartment_id]);
        if($check_fee == true){
            $ServiceElectricFeeCheck = $ServiceElectricFeeCheck->andWhere(['is_created_fee' => ServiceElectricFee::IS_UNCREATED_FEE]);
        }
        $ServiceElectricFeeCheck =  $ServiceElectricFeeCheck->orderBy(['lock_time' => SORT_ASC])->one();
        if(!empty($ServiceElectricFeeCheck)){
            return false;
        }

        $ServiceElectricInfo = ServiceElectricInfo::findOne(['building_cluster_id' => $this->building_cluster_id, 'apartment_id' => $this->apartment_id, 'service_map_management_id' => $this->service_map_management_id]);
        if($this->status === ServiceElectricFee::STATUS_ACTIVE){
            if(!empty($ServiceElectricInfo)){
//                if($ServiceElectricInfo->tmp_end_date === $this->lock_time){
                if($ServiceElectricInfo->end_index > $this->start_index){
                    $ServiceElectricInfo->end_index = $this->start_index;
                }
                $ServiceElectricInfo->tmp_end_date = strtotime('-1 day', $this->start_time);
                if($ServiceElectricInfo->end_date > $ServiceElectricInfo->tmp_end_date){
                    $ServiceElectricInfo->end_date = $ServiceElectricInfo->tmp_end_date;
                };
                if(!$ServiceElectricInfo->save()){
                    Yii::error($ServiceElectricInfo->errors);
                    return false;
                }
                if(!$this->delete()){
                    Yii::error($this->errors);
                    return false;
                }
                return true;
//                }
            }
            return false;
        }else{
            if(!empty($ServiceElectricInfo)){
                $ServiceElectricInfo->tmp_end_date = strtotime('-1 day', $this->start_time);
                if(!$ServiceElectricInfo->save()){
                    Yii::error($ServiceElectricInfo->errors);
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
