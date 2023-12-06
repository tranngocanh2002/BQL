<?php

namespace console\controllers;

use common\helpers\ApiHelper;
use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingArea;
use common\models\PaymentGenCode;
use common\models\PaymentGenCodeItem;
use common\models\ServiceBill;
use common\models\ServiceBillItem;
use common\models\ServiceBuildingConfig;
use common\models\ServiceBuildingFee;
use common\models\ServiceBuildingInfo;
use common\models\ServiceDebt;
use common\models\ServiceManagementVehicle;
use common\models\ServiceMapManagement;
use common\models\ServiceParkingFee;
use common\models\ServicePaymentFee;
use common\models\ServiceVehicleConfig;
use Exception;
use Yii;
use yii\console\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;


class ServiceController extends Controller
{
    /*
     * Del Bill Draft
     * chạy định kỳ 30 phút /1 lần
     */
    public function actionBillDeleteDraft()
    {
        die('Check lại cơ chế');
        $serviceBills = ServiceBill::find()
            ->where(['status' => ServiceBill::STATUS_DRAFT])
            ->andWhere(['<', 'created_at', time() - 60 * 30])->all();
        foreach ($serviceBills as $serviceBill) {
            ServicePaymentFee::updateAll(['service_bill_id' => null, 'service_bill_code' => null], ['service_bill_id' => $serviceBill->id]);
            $serviceBill->delete();
        }
    }

    /*
     * Del PaymentGenCode
     * chạy định kỳ 1 phút /1 lần
     */
    public function actionPaymentGenCodeDelete()
    {
        $paymentGenCodes = PaymentGenCode::find()
            ->where(['type' => PaymentGenCode::PAY_ONLINE, 'is_auto' => PaymentGenCode::IS_NOT_AUTO])
            ->andWhere(['<', 'lock_time', time()])->all();
        foreach ($paymentGenCodes as $paymentGenCode) {
            PaymentGenCodeItem::deleteAll(['payment_gen_code_id' => $paymentGenCode->id]);
            $paymentGenCode->delete();
        }
    }

    /*
     * tạo fee cho dịch vụ tòa nhà
     * đối với các căn hộ đã được tạo phí trước đó hoặc chưa được tạo phí và có ngày nhận nhà thuộc chi kỳ tạo phí
     * Chạy định kỳ 1 phút / 1 lần
     * lấy ra các dịch vụ phù hợp theo cấu hình để tạo fee
     */
    public function actionBuildingConfigFee()
    {
        die('Thay đổi cơ chế');
        echo 'Start Create';
        $time_current = time();
        $minute_current = (int)date('i');
        $hour_current = (int)date('H');
        $day_current = (int)date('d');
        $month_current = (int)date('m');
        $day_of_week_current = (int)date('w');
        $fee_of_month = strtotime(date('Y-m-01', time()));

        $serviceBuildingConfigs = ServiceBuildingConfig::find()
            ->where(['or',
                ['like', 'cr_minutes', ',' . $minute_current . ','],
                ['cr_minutes' => '*']
            ])
            ->andWhere(['or',
                ['like', 'cr_hours', ',' . $hour_current . ','],
                ['cr_hours' => '*']
            ])
            ->andWhere(['or',
                ['like', 'cr_days', ',' . $day_current . ','],
                ['cr_days' => '*']
            ])
            ->andWhere(['or',
                ['like', 'cr_months', ',' . $month_current . ','],
                ['cr_months' => '*']
            ])
            ->andWhere(['or',
                ['like', 'cr_days_of_week', ',' . $day_of_week_current . ','],
                ['cr_days_of_week' => '*']
            ])->all();

        foreach ($serviceBuildingConfigs as $serviceBuildingConfig) {
            $day_expired = strtotime("+" . $serviceBuildingConfig->offset_day . " day");
            $start_time = strtotime(date('Y-m-01 00:00:00', $time_current));
            $end_month_current = $month_current + $serviceBuildingConfig->month_cycle;
            $d = new \DateTime(date("Y-$end_month_current-01"));
            $end_time = strtotime($d->format("Y-m-t 23:59:59"));

            $current_month = date('m', $start_time);
            $total_month = $current_month;
            for ($i = 1; $i <= $serviceBuildingConfig->month_cycle - 1; $i++) {
                $total_month .= ',' . ($current_month + $i);
            }

            $apartments = Apartment::find()
                ->where(['building_cluster_id' => $serviceBuildingConfig->building_cluster_id, 'is_deleted' => Apartment::NOT_DELETED])
                ->andWhere(['not', ['resident_user_id' => null]])->all();

            foreach ($apartments as $apartment) {
                $is_create = 0;
                $serviceBuildingInfo = ServiceBuildingInfo::findOne(['apartment_id' => $apartment->id]);
                if (!empty($serviceBuildingInfo)) {
                    if ($serviceBuildingInfo->end_date < $end_time) {
                        $is_create = 1;
                    }
                } else {
                    $date_received = $apartment->date_received;
                    if ($month_current == date('m', $date_received) && $day_current >= date('d', $date_received)) {
                        $is_create = 1;
                    }
                }
                if ($is_create == 1) {
                    $json_desc = [];
                    $description = "Diện tích căn hộ: " . $apartment->capacity . "m2\n";
                    $json_desc['dien_tich'] = [
                        'text' => $description,
                        'capacity' => $apartment->capacity,
                        'unit' => 'm2'
                    ];
                    $price = $serviceBuildingConfig->price * $serviceBuildingConfig->month_cycle;
                    $text_fee = "Mức phí: " . CUtils::formatPrice($serviceBuildingConfig->price) . 'đ/Căn hộ/tháng';
                    $json_desc['muc_phi'] = [
                        'text' => $text_fee,
                        'price' => $serviceBuildingConfig->price,
                        'month_cycle' => $serviceBuildingConfig->month_cycle,
                        'unit' => 'Căn hộ/tháng'
                    ];
                    if ($serviceBuildingConfig->unit == ServiceBuildingConfig::UNIT_M2) {
                        $price = $serviceBuildingConfig->price * $apartment->capacity * $serviceBuildingConfig->month_cycle;
                        $text_fee = "Mức phí: " . CUtils::formatPrice($serviceBuildingConfig->price) . 'đ/m2/tháng';
                        $json_desc['muc_phi'] = [
                            'text' => $text_fee,
                            'price' => $serviceBuildingConfig->price,
                            'capacity' => $apartment->capacity,
                            'month_cycle' => $serviceBuildingConfig->month_cycle,
                            'unit' => 'm2/tháng'
                        ];
                    }
                    $description .= $text_fee;

                    //update thời gian xử dụng info
                    $serviceBuildingInfo = ServiceBuildingInfo::findOne(['building_cluster_id' => $serviceBuildingConfig->building_cluster_id, 'apartment_id' => $apartment->id, 'service_map_management_id' => $serviceBuildingConfig->service_map_management_id]);
                    if (empty($serviceBuildingInfo)) {
                        $serviceBuildingInfo = new ServiceBuildingInfo();
                        $serviceBuildingInfo->building_cluster_id = $serviceBuildingConfig->building_cluster_id;
                        $serviceBuildingInfo->building_area_id = $apartment->building_area_id;
                        $serviceBuildingInfo->apartment_id = $apartment->id;
                        $serviceBuildingInfo->service_map_management_id = $serviceBuildingConfig->service_map_management_id;
                        $serviceBuildingInfo->start_date = $start_time;
                        $serviceBuildingInfo->end_date = $end_time;
                        if (!$serviceBuildingInfo->save()) {
                            Yii::error($serviceBuildingInfo->errors);
                            return [
                                'success' => false,
                                'message' => Yii::t('frontend', "System busy"),
                            ];
                        }
                    } else {
                        if ($serviceBuildingInfo->end_date < $end_time) {
                            $serviceBuildingInfo->end_date = $end_time;
                            if (!$serviceBuildingInfo->save()) {
                                Yii::error($serviceBuildingInfo->errors);
                            }
                        }
                    }


                    //tạo phí tòa nhà
                    $serviceBuildingFee = new ServiceBuildingFee();
                    $serviceBuildingFee->service_map_management_id = $serviceBuildingConfig->service_map_management_id;
                    $serviceBuildingFee->service_building_config_id = $serviceBuildingConfig->id;
                    $serviceBuildingFee->building_cluster_id = $serviceBuildingConfig->building_cluster_id;
                    $serviceBuildingFee->building_area_id = $apartment->building_area_id;
                    $serviceBuildingFee->apartment_id = $apartment->id;
                    $serviceBuildingFee->total_money = $price;
                    $serviceBuildingFee->status = ServiceBuildingFee::STATUS_ACTIVE;
                    $serviceBuildingFee->description = $description;
                    $serviceBuildingFee->json_desc = json_encode($json_desc);
                    $serviceBuildingFee->start_time = $start_time;
                    $serviceBuildingFee->end_time = $end_time;
                    $serviceBuildingFee->count_month = $total_month;
                    if (!$serviceBuildingFee->save()) {
                        Yii::error($serviceBuildingFee->errors);
                    }

                    //tạo payment fee
                    $servicePaymentFee = new ServicePaymentFee();
                    $servicePaymentFee->service_map_management_id = $serviceBuildingConfig->service_map_management_id;
                    $servicePaymentFee->building_cluster_id = $serviceBuildingConfig->building_cluster_id;
                    $servicePaymentFee->building_area_id = $apartment->building_area_id;
                    $servicePaymentFee->apartment_id = $apartment->id;
                    $servicePaymentFee->price = $price;
                    $servicePaymentFee->status = ServicePaymentFee::STATUS_UNPAID;
                    $servicePaymentFee->fee_of_month = $fee_of_month;
                    $servicePaymentFee->day_expired = $day_expired;
                    $servicePaymentFee->description = $description;
                    $servicePaymentFee->json_desc = $serviceBuildingFee->json_desc;
                    $servicePaymentFee->type = ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE;
                    $servicePaymentFee->start_time = $start_time;
                    $servicePaymentFee->end_time = $end_time;
                    if (!$servicePaymentFee->save()) {
                        Yii::error($servicePaymentFee->getErrors());
                    }

                    $serviceBuildingFee->service_payment_fee_id = $servicePaymentFee->id;
                    if (!$serviceBuildingFee->save()) {
                        Yii::error($serviceBuildingFee->errors);
                    }
                }
            }
        }
        echo 'End Create';
    }

    /*
     * tạo fee cho dịch vụ tòa nhà
     * đối với những căn hộ chưa được tạo fee lần nào
     * Chạy định kỳ 15 phút / 1 lần / 2000 căn hộ
     * chia ra nhiều page để chạy, page cuối max size
     * lấy ra các căn hộ chưa được tạo phí và có ngày nhận nhà sau ngày tạo phí của cấu hình tương ứng
     */
    public function actionBuildingConfigFeeRetail($page = 1, $pageSize = 2000)
    {
        die('Thay đổi cơ chế');
        echo 'Start Create';
        $time_current = time();
        $minute_current = (int)date('i');
        $hour_current = (int)date('H');
        $day_current = (int)date('d');
        $month_current = (int)date('m');
        $day_of_week_current = (int)date('w');
        $fee_of_month = strtotime(date('Y-m-01', time()));

        //lấy ra danh sách căn hộ có chủ hộ theo page
        $query = Apartment::find()
            ->where(['is_deleted' => Apartment::NOT_DELETED])
            ->andWhere(['not', ['resident_user_id' => null]]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => $page - 1,
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ]
            ],
        ]);
        $apartments = $dataProvider->getModels();
        foreach ($apartments as $apartment) {
            $is_create = 0;
            $start_time = $time_current;
            $end_time = $time_current;
            $serviceBuildingInfo = ServiceBuildingInfo::findOne(['apartment_id' => $apartment->id]);
            if (empty($serviceBuildingInfo)) {
                $is_create = 1;
                $start_time = !empty($apartment->date_received) ? $apartment->date_received : $time_current;
            }

            if ($is_create == 1) {
                $serviceBuildingConfig = ServiceBuildingConfig::find()->where(['building_cluster_id' => $apartment->building_cluster_id])->one();
                if (empty($serviceBuildingConfig)) {
                    continue;
                }
                $current_month = date('m', $start_time);
                $total_month = $current_month;
                for ($i = 1; $i <= $serviceBuildingConfig->month_cycle - 1; $i++) {
                    $total_month .= ',' . ($current_month + $i);
                }

                $day = $serviceBuildingConfig->day;
                $cr_months = trim($serviceBuildingConfig->cr_months, ',');
                $arrMonths = explode($cr_months, ',');
                $month_cycle_new = 0;
                foreach ($arrMonths as $month) {
                    if ($month >= $month_current) {
                        $month_cycle_new = $month;
                        $d = new \DateTime(date("Y-$month-01"));
                        $end_time = strtotime($d->format("Y-m-t 23:59:59"));
                        break;
                    }
                }
                $month_cycle_old = $month_cycle_new - $serviceBuildingConfig->month_cycle;
                $y_back = 0;
                if ($month_cycle_old < 0) {
                    $month_cycle_old = 12 + $month_cycle_old;
                    $y_back = 1;
                }
                $day_create_fee = strtotime("-$y_back year", strtotime(date("Y-$month_cycle_old-$day 00:00:00")));
                if ($day_create_fee < $start_time) {
                    $diff_day_in_month = date_diff(date_create(date('Y-m-01', $start_time)), date_create(date('Y-m-01', strtotime('+1 month', strtotime(date('Y-m-01', $start_time))))));
                    $total_day_in_month = (int)$diff_day_in_month->format("%R%a");

                    $diff_day_use = date_diff(date_create(date('Y-m-d', $start_time)), date_create(date('Y-m-01', strtotime('+1 month', strtotime(date('Y-m-01', $start_time))))));
                    $total_day_use = (int)$diff_day_use->format("%R%a");

                    $description = "Diện tích căn hộ: " . $apartment->capacity . "m2\n";
                    $total_price = 0;
                    $json_desc['dien_tich'] = [
                        'text' => $description,
                        'capacity' => $apartment->capacity,
                        'unit' => 'm2'
                    ];

                    //số ngày lẻ cần tính tiền
                    if ($serviceBuildingConfig->unit == ServiceBuildingConfig::UNIT_M2) {
                        $retail_price = ($serviceBuildingConfig->price / $total_day_in_month) * $total_day_use * $apartment->capacity;
                        $text_fee = 'Mức phí lẻ: ' . CUtils::formatPrice($serviceBuildingConfig->price / $total_day_in_month) . ' x ' . $apartment->capacity . 'M2 x ' . $total_day_use . 'ngày' . ' = ' . CUtils::formatPrice($retail_price) . " đ\n";
                        $total_price += $retail_price;
                        $json_desc['muc_phi_le'] = [
                            'text' => $text_fee,
                            'price' => $serviceBuildingConfig->price,
                            'total_day_in_month' => $total_day_in_month,
                            'total_day_use' => $total_day_use,
                            'capacity' => $apartment->capacity,
                            'unit' => 'm2/ngày'
                        ];
                    } else {
                        $retail_price = ($serviceBuildingConfig->price / $total_day_in_month) * $total_day_use;
                        $text_fee = 'Mức phí lẻ: ' . CUtils::formatPrice($serviceBuildingConfig->price / $total_day_in_month) . ' x ' . $total_day_use . 'ngày' . ' = ' . CUtils::formatPrice($retail_price) . " đ\n";
                        $total_price += $retail_price;
                        $json_desc['muc_phi_le'] = [
                            'text' => $text_fee,
                            'price' => $serviceBuildingConfig->price,
                            'total_day_in_month' => $total_day_in_month,
                            'total_day_use' => $total_day_use,
                            'unit' => 'Căn hộ/ngày'
                        ];
                    }

                    //số tháng chẵn còn lại
                    if ($serviceBuildingConfig->month_cycle - 1 > 0) {
                        if ($serviceBuildingConfig->unit == ServiceBuildingConfig::UNIT_M2) {
                            $even_price = $serviceBuildingConfig->price * $apartment->capacity * ($serviceBuildingConfig->month_cycle - 1);
                            $total_price += $even_price;
                            $text_fee .= 'Mức phí tháng: ' . CUtils::formatPrice($serviceBuildingConfig->price) . ' x ' . $apartment->capacity . 'M2 x ' . ($serviceBuildingConfig->month_cycle - 1) . 'tháng' . ' = ' . CUtils::formatPrice($even_price) . " đ\n";
                            $json_desc['muc_phi_chan'] = [
                                'text' => 'Mức phí tháng: ' . CUtils::formatPrice($serviceBuildingConfig->price) . ' x ' . $apartment->capacity . 'M2 x ' . ($serviceBuildingConfig->month_cycle - 1) . 'tháng' . ' = ' . CUtils::formatPrice($even_price) . " đ",
                                'price' => $serviceBuildingConfig->price,
                                'month_cycle' => $serviceBuildingConfig->month_cycle - 1,
                                'capacity' => $apartment->capacity,
                                'unit' => 'm2/tháng'
                            ];
                        } else {
                            $even_price = $serviceBuildingConfig->price * ($serviceBuildingConfig->month_cycle - 1);
                            $total_price += $even_price;
                            $text_fee .= 'Mức phí tháng: ' . CUtils::formatPrice($serviceBuildingConfig->price) . ' x ' . ($serviceBuildingConfig->month_cycle - 1) . 'tháng' . ' = ' . CUtils::formatPrice($even_price) . " đ\n";
                            $json_desc['muc_phi_chan'] = [
                                'text' => 'Mức phí tháng: ' . CUtils::formatPrice($serviceBuildingConfig->price) . ' x ' . ($serviceBuildingConfig->month_cycle - 1) . 'tháng' . ' = ' . CUtils::formatPrice($even_price) . " đ",
                                'price' => $serviceBuildingConfig->price,
                                'month_cycle' => $serviceBuildingConfig->month_cycle - 1,
                                'unit' => 'Căn hộ/tháng'
                            ];
                        }

                    }
                    $description .= $text_fee;
                    $day_expired = strtotime("+" . $serviceBuildingConfig->offset_day . " day");

                    //update thời gian xử dụng info
                    $serviceBuildingInfo = ServiceBuildingInfo::findOne(['building_cluster_id' => $serviceBuildingConfig->building_cluster_id, 'apartment_id' => $apartment->id, 'service_map_management_id' => $serviceBuildingConfig->service_map_management_id]);
                    if (empty($serviceBuildingInfo)) {
                        $serviceBuildingInfo = new ServiceBuildingInfo();
                        $serviceBuildingInfo->building_cluster_id = $serviceBuildingConfig->building_cluster_id;
                        $serviceBuildingInfo->building_area_id = $apartment->building_area_id;
                        $serviceBuildingInfo->apartment_id = $apartment->id;
                        $serviceBuildingInfo->service_map_management_id = $serviceBuildingConfig->service_map_management_id;
                        $serviceBuildingInfo->start_date = $start_time;
                        $serviceBuildingInfo->end_date = $end_time;
                        if (!$serviceBuildingInfo->save()) {
                            Yii::error($serviceBuildingInfo->errors);
                            return [
                                'success' => false,
                                'message' => Yii::t('frontend', "System busy"),
                            ];
                        }
                    } else {
                        if ($serviceBuildingInfo->end_date < $end_time) {
                            $serviceBuildingInfo->end_date = $end_time;
                            if (!$serviceBuildingInfo->save()) {
                                Yii::error($serviceBuildingInfo->errors);
                            }
                        }
                    }

                    //tạo phí tòa nhà
                    $serviceBuildingFee = new ServiceBuildingFee();
                    $serviceBuildingFee->service_map_management_id = $serviceBuildingConfig->service_map_management_id;
                    $serviceBuildingFee->service_building_config_id = $serviceBuildingConfig->id;
                    $serviceBuildingFee->building_cluster_id = $serviceBuildingConfig->building_cluster_id;
                    $serviceBuildingFee->building_area_id = $apartment->building_area_id;
                    $serviceBuildingFee->apartment_id = $apartment->id;
                    $serviceBuildingFee->total_money = $total_price;
                    $serviceBuildingFee->status = ServiceBuildingFee::STATUS_ACTIVE;
                    $serviceBuildingFee->description = $description;
                    $serviceBuildingFee->json_desc = json_encode($json_desc);
                    $serviceBuildingFee->start_time = $start_time;
                    $serviceBuildingFee->end_time = $end_time;
                    $serviceBuildingFee->count_month = $total_month;
                    if (!$serviceBuildingFee->save()) {
                        Yii::error($serviceBuildingFee->errors);
                    }

                    //tạo payment fee
                    $servicePaymentFee = new ServicePaymentFee();
                    $servicePaymentFee->service_map_management_id = $serviceBuildingConfig->service_map_management_id;
                    $servicePaymentFee->building_cluster_id = $serviceBuildingConfig->building_cluster_id;
                    $servicePaymentFee->building_area_id = $apartment->building_area_id;
                    $servicePaymentFee->apartment_id = $apartment->id;
                    $servicePaymentFee->price = $total_price;
                    $servicePaymentFee->status = ServicePaymentFee::STATUS_UNPAID;
                    $servicePaymentFee->fee_of_month = $fee_of_month;
                    $servicePaymentFee->day_expired = $day_expired;
                    $servicePaymentFee->description = $description;
                    $servicePaymentFee->json_desc = $serviceBuildingFee->json_desc;
                    if (!$servicePaymentFee->save()) {
                        Yii::error($servicePaymentFee->getErrors());
                    }

                    $serviceBuildingFee->service_payment_fee_id = $servicePaymentFee->id;
                    if (!$serviceBuildingFee->save()) {
                        Yii::error($serviceBuildingFee->errors);
                    }
                }
            }
        }

        echo 'End Create';
    }

    /*
     * tạo phí dịch vụ định kỳ
     * Chạy định kỳ 15 phút / 1 lần
     * chia ra nhiều luồng để chạy
     * chạy định kỳ kiểm trả xem hết hạn phí so với thời điểm hiện tại để tạo phí tới cuối tháng hiện tại
     */
    function actionBuildingFee($s, $t)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            echo 'Start Create' . "\n";
            $time_current = time();
            $minute_current = (int)date('i');
            $hour_current = (int)date('H');
            $day_current = (int)date('d');
            $month_current = (int)date('m');
            $day_of_week_current = (int)date('w');
            $fee_of_month = strtotime(date('Y-m-01', $time_current));
            $day_expired = strtotime("+5 day");

            //lấy ra danh sách căn hộ có chủ hộ theo page
            $query = Apartment::find()
                ->where(['is_deleted' => Apartment::NOT_DELETED])
                ->andWhere(['not', ['resident_user_id' => null]])
                ->andWhere("id%$s=$t");
//            $dataProvider = new ActiveDataProvider([
//                'query' => $query,
//                'pagination' => [
//                    'page' => $page - 1,
//                    'pageSize' => $pageSize,
//                ],
//                'sort' => [
//                    'defaultOrder' => [
//                        'id' => SORT_ASC,
//                    ]
//                ],
//            ]);
//            $apartments = $dataProvider->getModels();
//            foreach ($apartments as $apartment) {

            foreach ($query->each() as $apartment) {
                $serviceBuildingConfig = ServiceBuildingConfig::findOne(['building_cluster_id' => $apartment->building_cluster_id]);
                if (empty($serviceBuildingConfig)) {
                    continue;
                }
                if(empty($serviceBuildingConfig->serviceMapManagement) || ($serviceBuildingConfig->serviceMapManagement->status == ServiceMapManagement::STATUS_INACTIVE)){
                    continue;
                }

                if($serviceBuildingConfig->auto_create_fee !== ServiceBuildingConfig::AUTO_CREATE_FEE){
                    continue;
                }

                $start_time = strtotime(date('Y-m-d 00:00:00', $time_current));
                $serviceBuildingInfo = ServiceBuildingInfo::findOne(['apartment_id' => $apartment->id]);
                if (empty($serviceBuildingInfo)) {
                    continue;

//                    //bỏ logic tạo info , bắt buộc phải có trước ở bước import
//                    $start_time = !empty($apartment->date_received) ? $apartment->date_received : $time_current;
//                    $serviceBuildingInfo = new ServiceBuildingInfo();
//                    $serviceBuildingInfo->building_cluster_id = $apartment->building_cluster_id;
//                    $serviceBuildingInfo->building_area_id = $apartment->building_area_id;
//                    $serviceBuildingInfo->apartment_id = $apartment->id;
//                    $serviceBuildingInfo->service_map_management_id = $serviceBuildingConfig->service_map_management_id;
//                    $serviceBuildingInfo->start_date = $start_time;
//                    $serviceBuildingInfo->end_date = $start_time;
//                    $serviceBuildingInfo->tmp_end_date = $start_time;
//                    if (!$serviceBuildingInfo->save()) {
//                        Yii::error($serviceBuildingInfo->errors);
//                        print_r($serviceBuildingInfo->errors);
//                        echo 'Create serviceBuildingInfo errors' . "\n";
//                        $transaction->rollBack();
//                        break;
//                    }
                } else {
                    if (!empty($serviceBuildingInfo->tmp_end_date)) {
                        $start_time = $serviceBuildingInfo->tmp_end_date;
                    }
                }
                if ($start_time > $time_current) {
                    continue;
                }
                $start_time = strtotime('+1 day', $start_time);
                echo 'start time: ' . $start_time . '-' . date('Y-m-d', $start_time) . "\n";
                $end_time = strtotime('+1 month', strtotime(date('Y-m-01', $start_time)));
                echo 'end time: ' . $end_time . '-' . date('Y-m-d', $end_time) . "\n";

                $ServiceBuildingFee = new ServiceBuildingFee();
                $ServiceBuildingFee->service_map_management_id = $serviceBuildingConfig->service_map_management_id;
                $ServiceBuildingFee->service_building_config_id = $serviceBuildingConfig->id;
                $ServiceBuildingFee->building_cluster_id = $apartment->building_cluster_id;
                $ServiceBuildingFee->building_area_id = $apartment->building_area_id;
                $ServiceBuildingFee->apartment_id = $apartment->id;
                $ServiceBuildingFee->count_month = 1;
                $ServiceBuildingFee->fee_of_month = $start_time;
                $ServiceBuildingFee->start_time = $start_time;
                $res = $ServiceBuildingFee::getCharge($ServiceBuildingFee->start_time, $ServiceBuildingFee->building_cluster_id, $ServiceBuildingFee->count_month, $apartment->capacity);
                $ServiceBuildingFee->description = $res['description'];
                $ServiceBuildingFee->description_en = $res['description_en'];
                $ServiceBuildingFee->total_money = $res['total_money'];
                $ServiceBuildingFee->end_time = $res['end_time'];
                $ServiceBuildingFee->json_desc = json_encode($res['json_desc']);
//                $ServiceBuildingFee->status = ServiceBuildingFee::STATUS_ACTIVE;
                if ($ServiceBuildingFee->total_money <= 0) {
                    Yii::error('Phí 0đ');
                    continue;
                }
                if (!$ServiceBuildingFee->save()) {
                    Yii::error($ServiceBuildingFee->errors);
                    print_r($ServiceBuildingFee->errors);
                    echo "update ServiceBuildingFee fee không thành công\n";
                    $transaction->rollBack();
                    break;
                } else {
                    echo "update ServiceBuildingFee fee thành công\n";
                }

//                //tạo payment fee
//                $servicePaymentFee = new ServicePaymentFee();
//                $servicePaymentFee->service_map_management_id = $serviceBuildingConfig->service_map_management_id;
//                $servicePaymentFee->building_cluster_id = $apartment->building_cluster_id;
//                $servicePaymentFee->building_area_id = $apartment->building_area_id;
//                $servicePaymentFee->apartment_id = $apartment->id;
//                $servicePaymentFee->price = $ServiceBuildingFee->total_money;
//                $servicePaymentFee->status = ServicePaymentFee::STATUS_UNPAID;
//                $servicePaymentFee->fee_of_month = $fee_of_month;
//                $servicePaymentFee->day_expired = $day_expired;
//                $servicePaymentFee->type = ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE;
//                $servicePaymentFee->start_time = $ServiceBuildingFee->start_time;
//                $servicePaymentFee->end_time = $ServiceBuildingFee->end_time;
//                $servicePaymentFee->description = $ServiceBuildingFee->description;
//                $servicePaymentFee->json_desc = $ServiceBuildingFee->json_desc;
//                if (!$servicePaymentFee->save()) {
//                    Yii::error($servicePaymentFee->getErrors());
//                    print_r($servicePaymentFee->errors);
//                    echo "create servicePaymentFee fee không thành công\n";
//                    $transaction->rollBack();
//                    break;
//                } else {
//                    echo "create servicePaymentFee fee thành công\n";
//                }
//
//                $ServiceBuildingFee->service_payment_fee_id = $servicePaymentFee->id;
//                if (!$ServiceBuildingFee->save()) {
//                    Yii::error($ServiceBuildingFee->errors);
//                    print_r($ServiceBuildingFee->errors);
//                    echo "update ServiceBuildingFee fee không thành công\n";
//                    $transaction->rollBack();
//                    break;
//                } else {
//                    echo "update ServiceBuildingFee fee thành công\n";
//                }

                $serviceBuildingInfo->tmp_end_date = $ServiceBuildingFee->end_time;
                if (!$serviceBuildingInfo->save()) {
                    Yii::error($serviceBuildingInfo->errors);
                    print_r($serviceBuildingInfo->errors);
                    echo "update serviceBuildingInfo fee không thành công\n";
                    $transaction->rollBack();
                    break;
                } else {
                    echo "update serviceBuildingInfo fee thành công\n";
                }

            }
            $transaction->commit();
            echo 'End Create';
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            print_r($ex->getMessage());
            echo "error";
        }
    }

    /*
     * tạo phí gửi xe định kỳ
     * Chạy định kỳ 15 phút / 1 lần
     * chia ra nhiều luồng để chạy
     * chạy định kỳ kiểm trả xem hết hạn phí so với thời điểm hiện tại để tạo phí tới cuối tháng hiện tại
     * $s : số luồng chạy đông thời
     * $t : số dư phép chia mỗi luồng
     * */
    function actionParkingFee($s, $t)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            echo 'Start Create' . "\n";
            $time_current = time();
            $minute_current = (int)date('i');
            $hour_current = (int)date('H');
            $day_current = (int)date('d');
            $month_current = (int)date('m');
            $day_of_week_current = (int)date('w');
            $fee_of_month = strtotime(date('Y-m-01', $time_current));
            $day_expired = strtotime("+5 day");

            //lấy ra danh sách xe theo page
            $query = ServiceManagementVehicle::find()
                ->where(['is_deleted' => ServiceManagementVehicle::NOT_DELETED, 'status' => ServiceManagementVehicle::STATUS_ACTIVE])
                ->andWhere("id%$s=$t");
//            $dataProvider = new ActiveDataProvider([
//                'query' => $query,
//                'pagination' => [
//                    'page' => $page - 1,
//                    'pageSize' => $pageSize,
//                ],
//                'sort' => [
//                    'defaultOrder' => [
//                        'id' => SORT_ASC,
//                    ]
//                ],
//            ]);
//            $serviceManagementVehicles = $dataProvider->getModels();
//            foreach ($serviceManagementVehicles as $serviceManagementVehicle) {

            foreach ($query->each() as $serviceManagementVehicle) {
                $serviceVehicleConfig = ServiceVehicleConfig::findOne(['building_cluster_id' => $serviceManagementVehicle->building_cluster_id]);
                if (empty($serviceVehicleConfig)) {
                    continue;
                }
                if(empty($serviceVehicleConfig->serviceMapManagement) || ($serviceVehicleConfig->serviceMapManagement->status == ServiceMapManagement::STATUS_INACTIVE)){
                    continue;
                }
                if ($serviceVehicleConfig->auto_create_fee !== ServiceVehicleConfig::AUTO_CREATE_FEE) {
                    continue;
                }

                $start_time = strtotime(date('Y-m-d 00:00:00', $time_current));
                //nếu chưa có ngày kết thúc thì gán bằng ngày đầu tháng
                if (!empty($serviceManagementVehicle->tmp_end_date)) {
                    $start_time = $serviceManagementVehicle->tmp_end_date;
                }
                if ($start_time > $time_current) {
                    continue;
                }
                $start_time = strtotime('+1 day', $start_time);
                echo 'start time: ' . $start_time . '-' . date('Y-m-d', $start_time) . "\n";
                $end_time = strtotime('+1 month', strtotime(date('Y-m-01', $start_time)));
                echo 'end time: ' . $end_time . '-' . date('Y-m-d', $end_time) . "\n";
                $cancel_date = $serviceManagementVehicle->cancel_date;
                $ServiceParkingFee = new ServiceParkingFee();
                $ServiceParkingFee->count_month = 1;
                $ServiceParkingFee->service_management_vehicle_id = $serviceManagementVehicle->id;
                $ServiceParkingFee->service_parking_level_id = $serviceManagementVehicle->service_parking_level_id;
                $ServiceParkingFee->service_map_management_id = $serviceManagementVehicle->serviceParkingLevel->service_map_management_id;
                $ServiceParkingFee->building_cluster_id = $serviceManagementVehicle->building_cluster_id;
                $ServiceParkingFee->building_area_id = $serviceManagementVehicle->building_area_id;
                $ServiceParkingFee->apartment_id = $serviceManagementVehicle->apartment_id;
                $ServiceParkingFee->fee_of_month = $start_time;
                $ServiceParkingFee->start_time = $start_time;
                $res = $ServiceParkingFee::getCharge($ServiceParkingFee->start_time, $ServiceParkingFee->service_parking_level_id, $ServiceParkingFee->count_month, $cancel_date);
                $ServiceParkingFee->description = $res['description'];
                $ServiceParkingFee->description_en = $res['description_en'];
                $ServiceParkingFee->total_money = $res['total_money'];
                $ServiceParkingFee->end_time = $res['end_time'];
                $ServiceParkingFee->json_desc = json_encode($res['json_desc']);
//                $ServiceParkingFee->status = ServiceParkingFee::STATUS_ACTIVE;
                if ($ServiceParkingFee->total_money <= 0) {
                    Yii::error('Phí 0đ');
                    continue;
                }
                if (!$ServiceParkingFee->save()) {
                    Yii::error($ServiceParkingFee->errors);
                    echo "Create fee xe không thành công\n";
                    $transaction->rollBack();
                    break;
                } else {
                    echo "Create fee xe thành công\n";
                }

//                //tạo payment fee
//                $servicePaymentFee = new ServicePaymentFee();
//                $servicePaymentFee->service_map_management_id = $ServiceParkingFee->service_map_management_id;
//                $servicePaymentFee->building_cluster_id = $ServiceParkingFee->building_cluster_id;
//                $servicePaymentFee->building_area_id = $ServiceParkingFee->building_area_id;
//                $servicePaymentFee->apartment_id = $ServiceParkingFee->apartment_id;
//                $servicePaymentFee->price = $ServiceParkingFee->total_money;
//                $servicePaymentFee->status = ServicePaymentFee::STATUS_UNPAID;
//                $servicePaymentFee->fee_of_month = $fee_of_month;
//                $servicePaymentFee->day_expired = $day_expired;
//                $servicePaymentFee->description = $ServiceParkingFee->description;
//                $servicePaymentFee->json_desc = $ServiceParkingFee->json_desc;
//                $servicePaymentFee->type = ServicePaymentFee::TYPE_SERVICE_PARKING_FEE;
//                $servicePaymentFee->start_time = $ServiceParkingFee->start_time;
//                $servicePaymentFee->end_time = $ServiceParkingFee->end_time;
//                if (!$servicePaymentFee->save()) {
//                    Yii::error($servicePaymentFee->getErrors());
//                    echo "create payment fee không thành công\n";
//                    $transaction->rollBack();
//                    break;
//                } else {
//                    echo "create payment fee thành công\n";
//                }
//
//                $ServiceParkingFee->service_payment_fee_id = $servicePaymentFee->id;
//                if (!$ServiceParkingFee->save()) {
//                    Yii::error($ServiceParkingFee->errors);
//                    echo "update parking fee không thành công\n";
//                    $transaction->rollBack();
//                    break;
//                } else {
//                    echo "update parking fee thành công\n";
//                }

                if (empty($serviceManagementVehicle->end_date)) {
                    $serviceManagementVehicle->end_date = $ServiceParkingFee->end_time;
                }
                $serviceManagementVehicle->tmp_end_date = $ServiceParkingFee->end_time;
                if(!empty($cancel_date) && (strtotime(date('Y-m-d 00:00:00', $cancel_date)) <= strtotime(date('Y-m-d 00:00:00', time())))){
                    $serviceManagementVehicle->status = ServiceManagementVehicle::STATUS_UNACTIVE;
                }
                if (!$serviceManagementVehicle->save()) {
                    Yii::error($serviceManagementVehicle->errors);
                    echo "update ManagementVehicle fee không thành công\n";
                    $transaction->rollBack();
                    break;
                } else {
                    echo "update ManagementVehicle fee thành công\n";
                }
            }
            $transaction->commit();
            echo 'End Create';
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            print_r($ex->getMessage());
            echo "error";
        }
    }

    /*
     * tạo công nợ định kỳ
     * Chạy định kỳ 1 tháng / 1 lần (chạy đầu tháng)
     * chia ra nhiều luồng để chạy
     * $time = 2019-08-01
     * $s : số luồng chạy đông thời
     * $t : số dư phép chia mỗi luồng
     */
    function actionDebt($s, $t, $time = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            echo 'Start Create' . "\n";
            if ($time == null) {
                $time_current = time();
            } else {
                $time_current = strtotime($time);
            }
            $current_month = strtotime(date('Y-m-01', $time_current));
            $old_month = strtotime("-1 month", $current_month);

            echo date("Y-m-d ", $current_month);
            echo date("Y-m-d ", $old_month);

            $query = Apartment::find()
                ->where(['is_deleted' => Apartment::NOT_DELETED])
                ->andWhere(['not', ['resident_user_id' => null]])
                ->andWhere("id%$s=$t");

            //Xóa all công nợ của tháng hiện tại
            // ServiceDebt::deleteAll(['type' => ServiceDebt::TYPE_CURRENT_MONTH]);// comment xóa công nợ tháng hiện tại
            $c = 0;
            foreach ($query->each() as $apartment) {
                echo $apartment->id ."\n";
                if (!$apartment->updateCurrentDebt($old_month, 0)) {
                    $transaction->rollBack();
                    Yii::error($apartment->errors);
                    die('error apartment');
                }
                $c++;
            }
            $transaction->commit();
            echo 'Total Create: '.$c;
            echo 'End Create';
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            print_r($ex->getMessage());
            echo "error";
        }
    }

    /*
    * tạo công nợ định kỳ của tháng hiện tại
    * Chạy định kỳ 30phút / 1 lần
    * chia ra nhiều luồng để chạy
    * $s : số luồng chạy đông thời
    * $t : số dư phép chia mỗi luồng
    */
    function actionDebtCurrentMonth($s, $t)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            echo 'Start Create' . "\n";
            if($t == 0){
                //Xóa all công nợ của tháng hiện tại
                // ServiceDebt::deleteAll(['type' => ServiceDebt::TYPE_CURRENT_MONTH]); // ẩn xóa công nợ
            }

            $query = Apartment::find()
                ->where(['is_deleted' => Apartment::NOT_DELETED])
                ->andWhere(['not', ['resident_user_id' => null]])
                ->andWhere("id%$s=$t");
            $c = 0;
            foreach ($query->each() as $apartment) {
                echo $apartment->id ."\n";
                if(!$apartment->updateCurrentDebt()){
                    $transaction->rollBack();
                    echo 'updateCurrentDebt Error';
                    die;
                }
                $c++;
            }
            $transaction->commit();
            echo 'Total Create: '.$c;
            echo 'End Create';
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            print_r($ex->getMessage());
            echo "error";
        }
    }
}