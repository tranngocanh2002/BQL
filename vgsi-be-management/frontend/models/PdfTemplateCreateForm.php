<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\helpers\ErrorCode;
use common\helpers\StringUtils;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementCategory;
use common\models\AnnouncementItemSend;
use common\models\AnnouncementTemplate;
use common\models\Apartment;
use common\models\BuildingArea;
use common\models\ServiceBuildingConfig;
use common\models\ServiceDebt;
use common\models\ServiceParkingFee;
use common\models\ServicePaymentFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PdfTemplateCreateForm")
 * )
 */
class PdfTemplateCreateForm extends Model
{
    public $apartment_id;

    public $campaign_type;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id', 'campaign_type'], 'required'],
            [['apartment_id', 'campaign_type'], 'integer'],
        ];
    }

    public function gen()
    {
        $apartment_id = $this->apartment_id;
        $campaign_type = $this->campaign_type;

        $apartment = Apartment::findOne(['id' => $apartment_id]);


//==================================================================
        /*
         * type : 0 - phí nước, 1 - phí dịch vụ, 2 - phí xe , 3 - điện
         */
        $start_current_month = strtotime(date('Y-m-01 00:00:00'));
        $end_current_month = strtotime(date('Y-m-t 23:59:59', $start_current_month));
        $start_time_building_fee = date('d/m/Y', $start_current_month);
        $end_time_building_fee = date('d/m/Y', $end_current_month);
        $start_time_water_fee = date('d/m/Y', strtotime('-1 month', $start_current_month));
        $end_time_water_fee = date('d/m/Y', strtotime('-1 month', $end_current_month));
        $start_time_electric_fee = date('d/m/Y', strtotime('-1 month', $start_current_month));
        $end_time_electric_fee = date('d/m/Y', strtotime('-1 month', $end_current_month));
        $start_time_parking_fee = date('d/m/Y', $start_current_month);
        $end_time_parking_fee = date('d/m/Y', $end_current_month);
        $start_time_booking_fee = date('d/m/Y', $start_current_month);
        $end_time_booking_fee = date('d/m/Y', $end_current_month);

//==================================================================
        $not_empty_building = true;
        //phí dịch vụ của tháng hiện tại
        $servicePaymentFee = ServicePaymentFee::find()
            ->where([
                'apartment_id' => $apartment->id,
                'status' => ServicePaymentFee::STATUS_UNPAID,
                'type' => ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE,
                'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
                'is_debt' => ServicePaymentFee::IS_DEBT
            ])
            ->andWhere(['>=', 'fee_of_month', $start_current_month])
            ->andWhere(['<=', 'fee_of_month', $end_current_month])
            ->one();
        $serviceBuildingConfig = null;
        if (!empty($servicePaymentFee)) {
            //lấy thông tin config phí dịch vụ
            $serviceBuildingConfig = ServiceBuildingConfig::findOne(['building_cluster_id' => $servicePaymentFee->building_cluster_id, 'service_map_management_id' => $servicePaymentFee->service_map_management_id]);
        }
        //phí dịch vụ nợ cũ -> trước tháng hiện tại
        $total_servicePaymentFee = ServicePaymentFee::find()
            ->select(["SUM(more_money_collecte) as more_money_collecte"])
            ->where([
                'apartment_id' => $apartment->id,
                'status' => ServicePaymentFee::STATUS_UNPAID,
                'type' => ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE,
                'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
                'is_debt' => ServicePaymentFee::IS_DEBT
            ])
            ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

        if(empty($servicePaymentFee) && (int)$total_servicePaymentFee->more_money_collecte == 0){
            $not_empty_building = false;
        }


//==================================================================
        $not_empty_parking = true;
        //phí gửi xe của tháng hiện tại
        $servicePaymentFeePackings = ServicePaymentFee::find()
            ->where([
                'apartment_id' => $apartment->id,
                'status' => ServicePaymentFee::STATUS_UNPAID,
                'type' => ServicePaymentFee::TYPE_SERVICE_PARKING_FEE,
                'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
                'is_debt' => ServicePaymentFee::IS_DEBT
            ])
            ->andWhere(['>=', 'fee_of_month', $start_current_month])
            ->andWhere(['<=', 'fee_of_month', $end_current_month])
            ->all();
        $FEE_PARKING = [];
        $total_parking_fee = 0;
        foreach ($servicePaymentFeePackings as $servicePaymentFeePacking){
            $serviceParkingFee = ServiceParkingFee::findOne(['service_payment_fee_id' => $servicePaymentFeePacking->id]);
            $number = '';
            if(!empty($serviceParkingFee)){
                if(!empty($serviceParkingFee->serviceManagementVehicle)){
                    $number = $serviceParkingFee->serviceManagementVehicle->number;
                }
            }
            $total_parking_fee += $servicePaymentFeePacking->more_money_collecte;
            $FEE_PARKING[] = [
                    'start_time' => $start_time_parking_fee,
                    'end_time' => $end_time_parking_fee,
                    'number' => $number,
                    'price' => CUtils::formatPrice($servicePaymentFeePacking->more_money_collecte)
                ];
        }

        //phí xe nợ cũ -> trước tháng hiện tại
        $total_servicePaymentFeePacking = ServicePaymentFee::find()
            ->select(["SUM(more_money_collecte) as more_money_collecte"])
            ->where([
                'apartment_id' => $apartment->id,
                'status' => ServicePaymentFee::STATUS_UNPAID,
                'type' => ServicePaymentFee::TYPE_SERVICE_PARKING_FEE,
                'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
                'is_debt' => ServicePaymentFee::IS_DEBT
            ])
            ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

        if(empty($servicePaymentFeePackings) && (int)$total_servicePaymentFeePacking->more_money_collecte == 0){
            $not_empty_parking = false;
        }


//==================================================================
        $not_empty_water = true;
        //phí nước tháng hiện tại
        $servicePaymentFeeWater = ServicePaymentFee::find()
            ->where([
                'apartment_id' => $apartment->id,
                'status' => ServicePaymentFee::STATUS_UNPAID,
                'type' => ServicePaymentFee::TYPE_SERVICE_WATER_FEE,
                'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
                'is_debt' => ServicePaymentFee::IS_DEBT
            ])
            ->andWhere(['>=', 'fee_of_month', $start_current_month])
            ->andWhere(['<=', 'fee_of_month', $end_current_month])
            ->one();

        $total_water_fee = 0;
        $json_desc = [];
        if (!empty($servicePaymentFeeWater)) {
            if (!empty($servicePaymentFeeWater->json_desc)) {
                $json_desc = json_decode($servicePaymentFeeWater->json_desc, true);
            }
            $json_desc['price'] = $servicePaymentFeeWater->more_money_collecte;
            $total_water_fee += $servicePaymentFeeWater->more_money_collecte;
        }

        //phí nước nợ cũ
        $total_servicePaymentFeeWater = ServicePaymentFee::find()
            ->select(["SUM(more_money_collecte) as more_money_collecte"])
            ->where([
                'apartment_id' => $apartment->id,
                'status' => ServicePaymentFee::STATUS_UNPAID,
                'type' => ServicePaymentFee::TYPE_SERVICE_WATER_FEE,
                'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
                'is_debt' => ServicePaymentFee::IS_DEBT
            ])
            ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

        if(empty($servicePaymentFeeWater) && (int)$total_servicePaymentFeeWater->more_money_collecte == 0){
            $not_empty_water = false;
        }


//==================================================================
        $not_empty_electric = true;
        //phí điện tháng hiện tại
        $servicePaymentFeeElectric = ServicePaymentFee::find()
            ->where([
                'apartment_id' => $apartment->id,
                'status' => ServicePaymentFee::STATUS_UNPAID,
                'type' => ServicePaymentFee::TYPE_SERVICE_ELECTRIC_FEE,
                'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
                'is_debt' => ServicePaymentFee::IS_DEBT
            ])
            ->andWhere(['>=', 'fee_of_month', $start_current_month])
            ->andWhere(['<=', 'fee_of_month', $end_current_month])
            ->one();

        $total_electric_fee = 0;
        $json_desc_electric = [];
        if (!empty($servicePaymentFeeElectric)) {
            if (!empty($servicePaymentFeeElectric->json_desc)) {
                $json_desc_electric = json_decode($servicePaymentFeeElectric->json_desc, true);
            }
            $json_desc_electric['price'] = $servicePaymentFeeElectric->more_money_collecte;
            $total_electric_fee += $servicePaymentFeeElectric->more_money_collecte;
        }

        //phí điện nợ cũ
        $total_servicePaymentFeeElectric = ServicePaymentFee::find()
            ->select(["SUM(more_money_collecte) as more_money_collecte"])
            ->where([
                'apartment_id' => $apartment->id,
                'status' => ServicePaymentFee::STATUS_UNPAID,
                'type' => ServicePaymentFee::TYPE_SERVICE_ELECTRIC_FEE,
                'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
                'is_debt' => ServicePaymentFee::IS_DEBT
            ])
            ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

        if(empty($servicePaymentFeeElectric) && (int)$total_servicePaymentFeeElectric->more_money_collecte == 0){
            $not_empty_electric = false;
        }


//==================================================================
        $not_empty_booking = true;
        //phí đặt tiện ích tháng hiện tại
        $servicePaymentFeeBooking = ServicePaymentFee::find()
            ->select(["SUM(more_money_collecte) as more_money_collecte"])
            ->where([
                'apartment_id' => $apartment->id,
                'status' => ServicePaymentFee::STATUS_UNPAID,
                'type' => ServicePaymentFee::TYPE_SERVICE_BOOKING_FEE,
                'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
                'is_debt' => ServicePaymentFee::IS_DEBT
            ])
            ->andWhere(['>=', 'fee_of_month', $start_current_month])
            ->andWhere(['<=', 'fee_of_month', $end_current_month])
            ->one();

        $total_booking_fee = 0;
        $json_desc_booking = [];
        if (!empty($servicePaymentFeeBooking)) {
            if (!empty($servicePaymentFeeBooking->json_desc)) {
                $json_desc_booking = json_decode($servicePaymentFeeBooking->json_desc, true);
            }
            $json_desc_booking['price'] = $servicePaymentFeeBooking->more_money_collecte;
            $total_booking_fee += $servicePaymentFeeBooking->more_money_collecte;
        }

        //phí đặt tiện ích nợ cũ
        $total_servicePaymentFeeBooking = ServicePaymentFee::find()
            ->select(["SUM(more_money_collecte) as more_money_collecte"])
            ->where([
                'apartment_id' => $apartment->id,
                'status' => ServicePaymentFee::STATUS_UNPAID,
                'type' => ServicePaymentFee::TYPE_SERVICE_BOOKING_FEE,
                'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
                'is_debt' => ServicePaymentFee::IS_DEBT
            ])
            ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

        if(empty($servicePaymentFeeBooking) && (int)$total_servicePaymentFeeBooking->more_money_collecte == 0){
            $not_empty_booking = false;
        }

//==================================================================
        $not_empty_old_debit = true;
        //phí nợ cũ trước chuyển giao
        $total_servicePaymentFeeOldDebit = ServicePaymentFee::find()
            ->select(["SUM(more_money_collecte) as more_money_collecte"])
            ->where([
                'apartment_id' => $apartment->id,
                'status' => ServicePaymentFee::STATUS_UNPAID,
                'type' => ServicePaymentFee::TYPE_SERVICE_OLD_DEBIT_FEE,
                'is_draft' => ServicePaymentFee::IS_NOT_DRAFT,
                'is_debt' => ServicePaymentFee::IS_DEBT
            ])
            ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

        if((int)$total_servicePaymentFeeOldDebit->more_money_collecte == 0){
            $not_empty_old_debit = false;
        }


//==================================================================
        //tổng nợ

//        $total_payment = ServicePaymentFee::find()->select(["SUM(more_money_collecte) as more_money_collecte"])->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => [ServicePaymentFee::TYPE_SERVICE_WATER_FEE, ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE, ServicePaymentFee::TYPE_SERVICE_PARKING_FEE, ServicePaymentFee::TYPE_SERVICE_ELECTRIC_FEE]])->one();
        $total_payment = ServicePaymentFee::find()->select(["SUM(more_money_collecte) as more_money_collecte"])->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT, 'is_debt' => ServicePaymentFee::IS_DEBT])->one();
        $totalPayment = 0;
        if (!empty($total_payment)) {
            $totalPayment = (int)$total_payment->more_money_collecte;
        }
        $announcementTemplate = AnnouncementTemplate::findOne(['building_cluster_id' => $apartment->building_cluster_id, 'type' => $campaign_type]);

        $config_price = 0;
        $building_price = 0;
        if (!empty($serviceBuildingConfig)) {
            $config_price = $serviceBuildingConfig->price;
        }
        if (!empty($servicePaymentFee)) {
            $building_price = $servicePaymentFee->price;
        }

//==================================================================
        $data = [
            'APARTMENT' => $apartment,
            'TOTAL_PAYMENT' => CUtils::formatPrice($totalPayment),
            'BUILDING_CLUSTER' => $announcementTemplate->buildingCluster,
            'MONTH/YEAR' => date('m/Y', time()),
            'DAY/MONTH/YEAR' => date('d/m/Y', time()),
            'NOT_EMPTY_PARKING' => $not_empty_parking,
            'FEE_PARKING' => $FEE_PARKING,
            'PARKING_NO_CU' => CUtils::formatPrice((int)$total_servicePaymentFeePacking->more_money_collecte),
            'PARKING_TOTAL_PRICE' => CUtils::formatPrice($total_parking_fee + (int)$total_servicePaymentFeePacking->more_money_collecte),
            'NOT_EMPTY_WATER' => $not_empty_water,
            'FEE_WATER' => [
                [
                    'start_time' => $start_time_water_fee,
                    'end_time' => $end_time_water_fee,
                    'start_index' => (!empty($json_desc)) ? $json_desc['month']['start_index'] : 0,
                    'end_index' => (!empty($json_desc)) ? $json_desc['month']['end_index'] : 0,
                    'total_index' => (!empty($json_desc)) ? $json_desc['month']['total_index'] : 0,
                    'price' => (!empty($json_desc)) ? CUtils::formatPrice($json_desc['price']) : 0,
                    'MT' => (!empty($json_desc)) ? implode("<br>", $json_desc['dm']) : '',
                ]
            ],
            'WATER_NO_CU' => CUtils::formatPrice((int)$total_servicePaymentFeeWater->more_money_collecte),
            'WATER_TOTAL_PRICE' => CUtils::formatPrice($total_water_fee + (int)$total_servicePaymentFeeWater->more_money_collecte),
            'NOT_EMPTY_ELECTRIC' => $not_empty_electric,
            'FEE_ELECTRIC' => [
                [
                    'start_time' => $start_time_electric_fee,
                    'end_time' => $end_time_electric_fee,
                    'start_index' => (!empty($json_desc_electric)) ? $json_desc_electric['month']['start_index'] : 0,
                    'end_index' => (!empty($json_desc_electric)) ? $json_desc_electric['month']['end_index'] : 0,
                    'total_index' => (!empty($json_desc_electric)) ? $json_desc_electric['month']['total_index'] : 0,
                    'price' => (!empty($json_desc_electric)) ? CUtils::formatPrice($json_desc_electric['price']) : 0,
                    'MT' => (!empty($json_desc_electric)) ? implode("<br>", $json_desc_electric['dm']) : '',
                ]
            ],
            'ELECTRIC_NO_CU' => CUtils::formatPrice((int)$total_servicePaymentFeeElectric->more_money_collecte),
            'ELECTRIC_TOTAL_PRICE' => CUtils::formatPrice($total_electric_fee + (int)$total_servicePaymentFeeElectric->more_money_collecte),
            'NOT_EMPTY_BOOKING' => $not_empty_booking,
            'FEE_BOOKING' => [
                [
                    'start_time' => $start_time_booking_fee,
                    'end_time' => $end_time_booking_fee,
                    'price' => (!empty($json_desc_booking)) ? CUtils::formatPrice($json_desc_booking['price']) : 0,
                ]
            ],
            'BOOKING_NO_CU' => CUtils::formatPrice((int)$total_servicePaymentFeeBooking->more_money_collecte),
            'BOOKING_TOTAL_PRICE' => CUtils::formatPrice($total_booking_fee + (int)$total_servicePaymentFeeBooking->more_money_collecte),
            'NOT_EMPTY_OLD_DEBIT' => $not_empty_old_debit,
            'OLD_DEBIT_NO_CU' => CUtils::formatPrice((int)$total_servicePaymentFeeOldDebit->more_money_collecte),
            'NOT_EMPTY_BUILDING' => $not_empty_building,
            'FEE_BUILDING' => [
                [
                    'start_time' => $start_time_building_fee,
                    'end_time' => $end_time_building_fee,
                    'config_price' => CUtils::formatPrice($config_price),
                    'price' => CUtils::formatPrice($building_price)
                ]
            ],
            'BUILDING_NO_CU' => CUtils::formatPrice((int)$total_servicePaymentFee->more_money_collecte),
            'BUILDING_TOTAL_PRICE' => CUtils::formatPrice($building_price + (int)$total_servicePaymentFee->more_money_collecte),
        ];
        $m = new \Mustache_Engine();
        echo $m->render($announcementTemplate->content_pdf, $data); // "Hello, World!"
    }
}
