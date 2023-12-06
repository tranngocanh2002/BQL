<?php

namespace console\controllers;

use common\helpers\ApiHelper;
use common\helpers\CgvVoiceOtp;
use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\helpers\Encoder;
use common\helpers\ErrorCode;
use common\helpers\MyCurl;
use common\helpers\OneSignalApi;
use common\helpers\payment\MomoPay;
use common\helpers\QueueLib;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementItem;
use common\models\AnnouncementSurvey;
use common\models\AnnouncementTemplate;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingArea;
use common\models\Job;
use common\models\ManagementNotifyReceiveConfig;
use common\models\ManagementUser;
use common\models\PaymentGenCode;
use common\models\PaymentGenCodeItem;
use common\models\ResidentUser;
use common\models\ResidentUserDeviceToken;
use common\models\ServiceBill;
use common\models\ServiceBillItem;
use common\models\ServiceBuildingConfig;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityConfig;
use common\models\ServiceUtilityPrice;
use common\models\ServiceWaterFee;
use common\models\ServiceWaterInfo;
use Exception;
use frontend\models\ServiceBillResponse;
use frontend\models\ServicePaymentFeeResponse;
use kartik\mpdf\Pdf;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\console\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class TestController extends Controller
{
    function actionDiff(){
        $string = "Khu/A";
        if (preg_match('/[@#$%^&*()!.,\';:"`~=<>?|]/', $string)) {
            echo "Chuỗi chứa ít nhất một ký tự đặc biệt.";
        } else {
            echo "Chuỗi không chứa ký tự đặc biệt.";
        }
        die;
        $is_check_inject = true;
        $inJectModels = [
            'callback' => [
                'sms',
                'email',
                'notify',
            ]
        ];
        if($is_check_inject == true && isset($inJectModels['callback'])){
            if(in_array('email', $inJectModels['callback'])){
                die('1111');
            }
        }
        die($is_check_inject);
        $str_date = '10/11/2023';
        $dateTime = \DateTime::createFromFormat('d/m/Y', $str_date);
        var_dump($dateTime);
        $errors = \DateTime::getLastErrors();
        if (!empty($errors['warning_count'])) {
            die('1');
        }
        die('0');

       $job = Job::findOne(201);
       echo $job->diffDate();
    }
    function actionVoiceOtp(){
        $voiceOtp = new CgvVoiceOtp();
        $voiceOtp->sendOtpVoice('1234', '0396899593');
    }

    function actionGetMap(){
        echo implode(',', ['ssss', 'ssaaaa']);
        die;
       $apartment = Apartment::findOne(1);
       if(!empty($apartment->apartmentMapResidentUserReceiveNotifyFee)){
           echo $apartment->apartmentMapResidentUserReceiveNotifyFee->email;
       }
    }

    function actionCb(){
        $sql = Yii::$app->db;
        $sums = AnnouncementSurvey::find()->select('status, count(*) as total_answer, sum(apartment_capacity) as apartment_capacity')->where(['announcement_campaign_id' => 1])->groupBy(['status'])->all();
        $barChart = [];
        foreach ($sums as $sum) {
            $barChart[] = [
                'status' => $sum->status,
                'total_apartment_capacity' => $sum->apartment_capacity,
                'total_answer' => $sum->total_answer,
            ];
        }

        $countByService = $sql->createCommand("select FROM_UNIXTIME(updated_at, '%Y-%m-%d') as report_day, count(*) as total from announcement_survey where announcement_campaign_id = 1 and status in (".AnnouncementSurvey::STATUS_AGREE.",".AnnouncementSurvey::STATUS_DISAGREE.") group by FROM_UNIXTIME(updated_at, '%Y-%m-%d') order by FROM_UNIXTIME(updated_at, '%Y-%m-%d') DESC")->queryAll();
        $pieChart = [];
        foreach ($countByService as $row){
            $pieChart[] = [
                'report_day' => $row['report_day'],
                'total' => (int)$row['total'],
            ];
        }

        var_dump($barChart);
        var_dump($pieChart);
    }

    private function abc(&$a){
        $a++;
    }
    function actionFee(){
        $action = ['action_create' => ManagementNotifyReceiveConfig::NOT_RECEIVED];
        $query = [
            'building_cluster_id' => 1,
            'management_user_id' => 2,
            'channel' => ManagementNotifyReceiveConfig::CHANNEL_NOTIFY_APP,
            'type' => ManagementNotifyReceiveConfig::TYPE_FEE,
        ];
        $query = array_merge($query, $action);
        var_dump($query);
    }

    function actionTestDebt($apartment_id, $time = null){
        echo 'Start Create' . "\n";
        $time_current = time();
        if(!empty($time)){
            $time_current = strtotime($time);
        }
        echo date('Y-m-01', $time_current)."\n";
        $current_month = strtotime(date('Y-m-01', $time_current));
        echo $current_month."\n";
        $start_old_month = strtotime(date('Y-m-01 00:00:00', $current_month));
        $d = new \DateTime(date('Y-m-01 00:00:00', $current_month));
        $end_old_month = strtotime($d->format( 'Y-m-t 23:59:59' ));
        $apartment = Apartment::findOne(['id' => $apartment_id]);
        $early_debt = $apartment->getEarlyDebt($current_month);
        echo "$early_debt\n";
        die;
    }


    public function actionBookingCode(){
        $bookings = ServiceUtilityBooking::find()->where(['code' => null])->all();
        foreach ($bookings as $booking){
            $system_code = "";
            while (empty($system_code)) {
                $system_code = "BK" . CUtils::generateRandomString(2) . CUtils::generateRandomNumber(4);
                if (ServiceUtilityBooking::findOne(['code' => $system_code, 'building_cluster_id' => $booking->building_cluster_id])) {
                    $system_code = "";
                }
            }
            $booking->code = $system_code;
            if(!$booking->save()){
                echo 'Error '. $booking->id . "\r\n";
            }
        }
    }

    public function actionRsa()
    {
        $a = [
            "partnerCode" => "MOMOIQA420180417",
            "partnerRefId" => "Merchant123556666",
            "partnerTransId" => "8374736463",
            "amount" => 40000,
            "description" => "Thanh toan momo"
        ];
        $h = Encoder::encryptRSA($a, MomoPay::PUBLIC_KEY);
        echo $h;
//        $b = Encoder::decryptRSA($h, $privateKey);
//        echo $b;
    }

    public function actionReport($building_cluster_id, $start_date, $end_date, $service_utility_config_id)
    {
        $serviceUtilityConfig = ServiceUtilityConfig::findOne(['id' => $service_utility_config_id, 'building_cluster_id' => $building_cluster_id]);
        if (empty($serviceUtilityConfig)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $times = [];
        $serviceUtilityPrices = ServiceUtilityPrice::find()->where(['service_utility_config_id' => $service_utility_config_id])->all();
        foreach ($serviceUtilityPrices as $serviceUtilityPrice) {
            $times[$serviceUtilityPrice->start_time . '_' . $serviceUtilityPrice->end_time] = [
                'start_time' => $serviceUtilityPrice->start_time,
                'end_time' => $serviceUtilityPrice->end_time,
                'total_slot' => $serviceUtilityConfig->total_slot,
                'total_slot_book' => 0
            ];
        }

        $start_date_min = strtotime(date('Y-m-d 00:00:00', $start_date));
        $start_date_max = strtotime('+1 day', strtotime(date('Y-m-d 00:00:00', $end_date)));
        $serviceUtilityBooks = ServiceUtilityBooking::find()->where(['service_utility_config_id' => $service_utility_config_id])
            ->andWhere(['>=', 'status', ServiceUtilityBooking::STATUS_CREATE])
            ->andWhere(['>=', 'start_time', $start_date_min])
            ->andWhere(['<=', 'end_time', $start_date_max])
            ->all();
        $bookByDates = [];
        foreach ($serviceUtilityBooks as $serviceUtilityBook) {
            $timeBooks = [];
            if (!empty($serviceUtilityBook->book_time)) {
                $book_time = json_decode($serviceUtilityBook->book_time, true);
                foreach ($book_time as $item) {
                    $timeBooks[] = [
                        'start_time' => $item['start_time'],
                        'end_time' => $item['end_time'],
                        'total_slot_book' => $serviceUtilityBook->total_slot,
                    ];
                }
            }
            $bookByDates[date('Ymd', $serviceUtilityBook->start_time)] = $timeBooks;
        }

        $dates = [];
        $date_time_next = $start_date_min;
        $i = 0;
        while ($date_time_next < $start_date_max) {
            $i++;
            $str_date_next = date('Ymd', $date_time_next);
            $time_news = $times;
            if (isset($bookByDates[$str_date_next])) {
                foreach ($bookByDates[$str_date_next] as $timeBook) {
                    foreach ($time_news as $k => $time_new) {
                        $start_time_in = strtotime(date('Y-m-d', $timeBook['start_time']) . ' ' . $time_new['start_time'] . ':00');
                        $end_time_in = strtotime(date('Y-m-d', $timeBook['end_time']) . ' ' . $time_new['end_time'] . ':00');
                        if ($start_time_in <= $timeBook['start_time'] && $end_time_in >= $timeBook['end_time']) {
                            $time_news[$k]['total_slot_book'] += $timeBook['total_slot_book'];
                        }
                    }
                }
            }
            $dates[$str_date_next] = [
                'date' => $date_time_next,
                'time' => array_values($time_news)
            ];
            $date_time_next = strtotime('+1 day', strtotime(date('Y-m-d 00:00:00', $date_time_next)));
            if ($i >= 100) {
                break;
            }
        }
        var_dump(array_values($dates));
        die;
    }

    public function actionFeeNoticesEmail()
    {
        $managementUser = ManagementUser::findOne(['id' => 19]);
        $managementUser->activeFeeNotices();
    }

    public function actionSms($phone, $otp_code)
    {
        $SmsCmc = Yii::$app->params['SmsCmc'];
//        $contentSms = "Ma OTP la " . $otp_code . ". Ma OTP chi dung mot lan de dang nhap  he thong!";
        $contentSms = "LUCI. Ma OTP la ".$otp_code.". Ma OTP chi dung mot lan de dang nhap he thong!";
//        $contentSms = "LUCI. Ma OTP la ".$otp_code.". Ma OTP chi dung mot lan de dang nhap  he thong!";
        $payload = [
            'to' => $phone,
            'utf' => true,
            'content' => $contentSms,
        ];
        $payload = array_merge($payload, $SmsCmc);
        QueueLib::channelSms(json_encode($payload), true);
    }

    public function actionGen()
    {
        $paymentGenCode = new PaymentGenCode();
        $paymentGenCode->building_cluster_id = 1;
        $paymentGenCode->apartment_id = 7;
        $paymentGenCode->generateCode();
        $paymentGenCode->type = 1;
        $paymentGenCode->is_auto = 1;
        $paymentGenCode->save();
        $servicePaymentFees = ServicePaymentFee::find()->where(['apartment_id' => $paymentGenCode->apartment_id])->all();
        foreach ($servicePaymentFees as $servicePaymentFee) {
            $paymentGenCodeItem = new PaymentGenCodeItem();
            $paymentGenCodeItem->building_cluster_id = $paymentGenCode->building_cluster_id;
            $paymentGenCodeItem->payment_gen_code_id = $paymentGenCode->id;
            $paymentGenCodeItem->service_payment_fee_id = $servicePaymentFee->id;
            $paymentGenCodeItem->amount = $servicePaymentFee->price;
            if (!$paymentGenCodeItem->save()) {
                print_r($paymentGenCodeItem->errors);
            };
        }
    }

    static function createPdf($content_html, $name_file)
    {
        $pdf = Yii::$app->pdf;
        $mpdf = $pdf->api;
        $mpdf->WriteHtml($content_html);
        $filename = Yii::getAlias('@frontend') . "/web/uploads/fee/$name_file";
        $mpdf->Output($filename, 'F');
        $pdf->render();
        return true;
    }

    public function actionTem()
    {
        $a = [
            'FEE_BUILDING' => [
                'config_price' => 0,
                'price' => 100
            ]
        ];
        $an = AnnouncementTemplate::findOne(['id' => 1]);
        $m = new \Mustache_Engine();
        echo $m->render($an->content_pdf, $a); // "Hello, World!"
        die;
        $campaign_type = 1;
        $apartment_id = 29;
        $apartment = Apartment::findOne(['id' => $apartment_id]);
        /*
 * type : 0 - phí nước, 1 - phí dịch vụ, 2 - phí xe
 */
        $start_current_month = strtotime(date('Y-m-01 00:00:00'));
        $end_current_month = strtotime(date('Y-m-t 23:59:59', $start_current_month));
//phí dịch vụ của tháng hiện tại
        $servicePaymentFee = ServicePaymentFee::find()
            ->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE])
            ->andWhere(['>=', 'fee_of_month', $start_current_month])
            ->andWhere(['<=', 'fee_of_month', $end_current_month])
            ->one();
        $serviceBuildingConfig = null;
        if (!empty($servicePaymentFee)) {
            //lấy thông tin config phí dịch vụ
            $serviceBuildingConfig = ServiceBuildingConfig::findOne(['building_cluster_id' => $servicePaymentFee->building_cluster_id, 'service_map_management_id' => $servicePaymentFee->service_map_management_id]);
        }
//phí dịch vụ nợ cũ -> trước tháng hiện tại
        $total_servicePaymentFee = ServicePaymentFee::find()->select(["SUM(more_money_collecte) as more_money_collecte"])->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE])
            ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

//phí nước tháng hiện tại
        $servicePaymentFeeWater = ServicePaymentFee::find()
            ->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => ServicePaymentFee::TYPE_SERVICE_WATER_FEE])
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
        $total_servicePaymentFeeWater = ServicePaymentFee::find()->select(["SUM(more_money_collecte) as more_money_collecte"])->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => ServicePaymentFee::TYPE_SERVICE_WATER_FEE])
            ->andWhere(['<', 'fee_of_month', $start_current_month])->one();

//tổng nợ
        $total_payment = ServicePaymentFee::find()->select(["SUM(more_money_collecte) as more_money_collecte"])->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'type' => [ServicePaymentFee::TYPE_SERVICE_WATER_FEE, ServicePaymentFee::TYPE_SERVICE_BUILDING_FEE]])->one();
        $totalPayment = 0;
        if (!empty($total_payment)) {
            $totalPayment = (int)$total_payment->more_money_collecte;
        }
        $announcementTemplate = AnnouncementTemplate::findOne(['building_cluster_id' => $apartment->building_cluster_id, 'type' => $campaign_type]);

        $config_price = 0;
        if (!empty($serviceBuildingConfig) && !empty($servicePaymentFee)) {
            $config_price = $serviceBuildingConfig->price;
        }
        $data = [
            'APARTMENT' => $apartment,
            'NOT_EMPTY_WATER' => true,
            'FEE_WATER' => [
                [
                    'start_index' => (!empty($json_desc)) ? $json_desc['month']['start_index'] : 0,
                    'end_index' => (!empty($json_desc)) ? $json_desc['month']['end_index'] : 0,
                    'total_index' => (!empty($json_desc)) ? $json_desc['month']['total_index'] : 0,
                    'price' => (!empty($json_desc)) ? $json_desc['month']['price'] : 0,
                    'MT' => (!empty($json_desc)) ? $json_desc['month']['dm'] : '',
                ]
            ],
            'WATER_NO_CU' => CUtils::formatPrice((int)$total_servicePaymentFeeWater->more_money_collecte),
            'WATER_TOTAL_PRICE' => CUtils::formatPrice($total_water_fee + (int)$total_servicePaymentFeeWater->more_money_collecte),
            'TOTAL_PAYMENT' => $totalPayment,
            'BUILDING_CLUSTER' => $announcementTemplate->buildingCluster,
            'MONTH/YEAR' => date('m/Y', time()),
            'DAY/MONTH/YEAR' => date('d/m/Y', time()),
            'NOT_EMPTY_BUILDING' => true,
            'FEE_BUILDING' => [
                'config_price' => CUtils::formatPrice($config_price),
                'price' => CUtils::formatPrice($servicePaymentFee->price)
            ],
            'BUILDING_NO_CU' => CUtils::formatPrice((int)$total_servicePaymentFee->more_money_collecte),
            'BUILDING_TOTAL_PRICE' => CUtils::formatPrice($servicePaymentFee->price + (int)$total_servicePaymentFee->more_money_collecte),
        ];

        $m = new \Mustache_Engine();
        echo $m->render($announcementTemplate->content_pdf, $data); // "Hello, World!"
    }

    public function actionSendNotify($resident_user_id)
    {
        $residentUserDeviceTokens = ResidentUserDeviceToken::find()->where(['resident_user_id' => $resident_user_id])->all();
        $title = "test notify";
        $content = "test notify";
        $data = [
            'type' => 'notify',
        ];
        $player_ids = [];
        foreach ($residentUserDeviceTokens as $residentUserDeviceToken){
            $player_ids[] = $residentUserDeviceToken->device_token;
        }
        $oneSignalApi = new OneSignalApi();
        $oneSignalApi->sendToWorkerPlayerIds($title, $content, $title, $content, $player_ids, $data, null, null);
    }

    public function actionSendEmail($email)
    {
        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailTest-html']
            )
            ->setFrom(['support@luci.vn' => 'Support Luci'])
            ->setTo($email)
            ->setSubject('Thông báo')
//            ->attachContent($file_img, ['fileName' => 'air-book.png', 'contentType' => 'image/png'])
            ->send();
    }

    public function actionSendEmailAvs($email)
    {
        $aws = Yii::$app->params['aws'];
        $payload = [
            'sender' => utf8_encode('nhu luc') . ' <' . $aws['sender'] . '>',
            'aws_config' => $aws['config'],
            'subject' => 'test send avs',
            'content' => '<div>Toi la luc</div>',
        ];
        $payload['to'] = [$email];
        QueueLib::channelEmailAws(Json::encode($payload));
    }


    public function actionTest()
    {
        $serviceBookings = ServiceUtilityBooking::find()->all();
        foreach ($serviceBookings as $serviceBooking){
            $serviceBooking->save();
        }
//        $str = 'MAI THỊ HỒNG ĐÀO';
        $residents = ResidentUser::find()->all();
        foreach ($residents as $resident){
            $resident->save();
        }
        $mapResidents = ApartmentMapResidentUser::find()->where(['is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all();
        foreach ($mapResidents as $mapResident){
            $mapResident->save();
        }
        $apartments = Apartment::find()->all();
        foreach ($apartments as $apartment){
            $apartment->save();
        }
        die;
//            $str = preg_replace('/[^\p{L}\s]/u','',$resident->first_name);
//            $str = CVietnameseTools::removeSigns2($resident->first_name);
//            echo CVietnameseTools::toLower($str)."\r\n";
//        $str = str_replace(' ', '-', $str); // Replaces all spaces with hyphens.

//        $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str); // Removes special chars.
        echo $str;die;

        $str1 = 'MAI THỊ HỒNG ĐÀO';
        echo mb_convert_encoding($str, 'UTF-8', 'ASCII')."\r\n";
        echo mb_convert_encoding($str1, 'UTF-8', 'ASCII')."\r\n";
die;
////        $str = preg_replace( '/ /', '|', $str );
//        $str = CVietnameseTools::removeSigns2($str);
//        $str1 = CVietnameseTools::removeSigns2($str1);
////        $str = CVietnameseTools::toLower($str);
////        $str = preg_replace( '/ /', '', $str );
////        $str = preg_replace( '/\|[^\p{L}\p{N}]+/u', '', $str );
////        $str = preg_replace( '/ /', '', $str );
////        $str = preg_replace( '/\|[a-zA-Z]+/u', '', $str );
//////        $str = preg_replace( '/\|/', ' ', $str );
//////        $str = preg_replace('/\P{Xan}+/u', ' ', $str );
//        echo $str. "\r\n";
//        echo $str1;
//        die;
        foreach(mb_list_encodings() as $chr){
            echo mb_convert_encoding($str, 'UTF-8', $chr)." : ".$chr."\r\n";
        }
        die($str);
        $a = Yii::$app->params['ConfigActionShowLog'];
        $actions = [];
        foreach ($a as $k => $v) {
            $actions = array_merge($actions, array_keys($v['actions']));
        }
        print_r($actions);
        die;
//        $a = [1,3,5];
//        $b = [2,3,6];
//        echo gettype($a);
//        $a = array_merge($a, $b);
//        $a = array_unique($a);
//        print_r($a);
//        echo gettype($a);
//        die;
        echo date('Y-m-d H:i:s', 1566179789);
        die;
        $item = AnnouncementItem::findOne(['id' => 1]);
        if (!empty($item->announcementItemSend)) {
            echo $item->announcementItemSend->id;
        }
        die;
        $count_email = ResidentUser::find()->select(["COUNT(resident_user.email) as email"])
            ->join('LEFT JOIN', 'apartment', 'resident_user.id = apartment.resident_user_id')
            ->where(['apartment.reminder_debt' => 0])->one();
        print_r($count_email->email);
        die;
//        $d = new \DateTime(date('Y-06-01 23:59:59'));
//        $end_old_month = strtotime($d->format( 'Y-m-t 23:59:59' ));
//        echo date('Y-m-d H:i:s', $end_old_month);
//        die;
////        $time_current = time();
//        $time_current = strtotime(date('Y-06-d H:i:s'));
//        $current_month = strtotime(date('Y-m-01', $time_current));
//        $start_old_month = strtotime(date('Y-m-01 00:00:00', $current_month));
//        $d = new \DateTime(date('Y-m-01 00:00:00', $current_month));
//        $end_old_month = strtotime($d->format( 'Y-m-t 23:59:59' ));
//        echo date('Y-m-d H:i:s', 1559111990);
//        die;

        $year = 2019;
        $month = '02';
//        $d = date($year.'-'.$month.'-t 23:59:59');
        $d = date('Y-m-d');
        echo $d;
        die;
        echo "\n";
        $t = strtotime($d);
        echo "\n";
        echo date('Y-m-d H:i:s', $t);
        echo "\n";
//        echo date('Y-m-01 H:i:s', 1562000399);
//        echo "\n";
//        echo date('Y-m-t H:i:s', 1564562881);
//        echo "\n";
//        echo date('Y-m-t H:i:s', 1564592400);
//        echo "\n";
        die;
        echo strtotime(date('2019-08-07 00:00:00'));
        echo "\n";
        echo strtotime(date('2019-08-08 00:00:00'));
        die;
        $bill = ServiceBillResponse::find()->andWhere(['not in', 'status', [ServiceBill::STATUS_DRAFT, ServiceBill::STATUS_CANCEL]])->all();
        foreach ($bill as $a) {
            echo $a->status;
            echo "\n";
        }
        die;
        echo date('Y-m-d H:i:s', 1564592400);
        echo "\n";
        echo date('Y-m-d H:i:s', 1562605200);
//        echo strtotime(date('Y-07-15 H:i:s'));
        die;
        $a = "1,3";
        $ar = explode(',', $a);
        print_r($ar);
        die;
        $servicePaymentFee = ServicePaymentFee::findOne(['id' => 1]);
        if (!empty($servicePaymentFee->service_bill_codes)) {
            $ids = json_decode($servicePaymentFee->service_bill_codes, true);
            $ids = array_diff($ids, ['RCUG9B']);
            $ids_new = [];
            foreach ($ids as $id) {
                $ids_new[] = $id;
            }
            $servicePaymentFee->service_bill_ids = json_encode($ids_new);
            $servicePaymentFee->save();
        }
        die;

        $a = [];
        $a[] = 2;
        $a[] = 4;
        $a[] = 14;
        $a = array_diff($a, [4]);
//        if (($key = array_search(4, $a)) !== false) {
//            unset($a[$key]);
//        }
        print_r($a);
//        $b = [];
//        $b['x'] = 'x';
//        $b['y'] = 'y';
//        $b['z'] = 'z';
//        print_r($b);
//        unset($b['y']);
//        print_r($b);
//
        die;

        $url = "https://graph.facebook.com/me?access_token=EAANIhjZAWc3QBAJXWVMyc4j1LlYwY4XyuZCB1MuzszuVX7N1bdd6eotQlfALpz0zpzeoEkV7iZC5TvcZADN7NZAvypmIFTN8lzrLsGzAWB3Q662ZCqYwrC7hFn3fw0RTkbNZAmK8F5fQfZAZCYt0I9hxUW7TWkyZC5gahGWoJ2LWx5uzeWyhqT70fUFkIyHYR021PCQKzNW8Ur7JailMFw35kiZAyBFphkg5y9I2IFqQA1DpgZDZD";
//        $curl = new MyCurl();
//        $data = $curl->get($url);
        $data = file_get_contents($url);
        $data = json_decode($data, true);
        echo $data['id'];
        die;
        $query = ServicePaymentFeeResponse::find()->select(["SUM(price) as price", "SUM(money_collected) as money_collected", "SUM(more_money_collecte) as more_money_collecte"])->where(['building_cluster_id' => 1]);
        $query->andWhere(['like', "FROM_UNIXTIME(fee_of_month, '%d/%m/%Y')", "/07/"]);
        $as = $query->one();
        print_r($as->price);
        print_r($as->money_collected);
        print_r($as->more_money_collecte);
        die;
        foreach ($as as $a) {
            print_r($a->price);
//            echo date('Y-m-d',$a->fee_of_month);
            echo "\r\n";
        }
        die;
        $year = 2019;
        $month = 19;
        $start_time = date($year . '-' . $month . '-01 00:00:00');
        $end_time = date($year . '-' . $month . '-t 23:59:59');
        echo $start_time . ' ' . $end_time;
        die;
        echo date('Y-m-t H:i:s', 1561914000);
        die;
        $start_time = strtotime(date('Y-07-20'));
        $end_time = strtotime(date('Y-10-20'));
//        $start_month = date('Y-m-01 00:00:00', time());
//        $end_month = date('Y-m-01 00:00:00', strtotime('+1 month', time()));
//        echo $start_month . '===' . $end_month;
        $diff_day_in_month = date_diff(date_create(date('Y-m-01', $start_time)), date_create(date('Y-m-01', $end_time)));
        print_r($diff_day_in_month->m);
    }

    public function actionCreateExcel($file_path = null)
    {
        if ($file_path == null) {
//            $file_path = '/uploads/applications/201907/'.time().'-test_nuoc.xlsx';
            $file_path = $fxls = dirname((dirname(__DIR__))) . "/db_desgin/" . time() . '-test.xlsx';
        }
//        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $fileandpath = $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên căn hộ');
        $sheet->setCellValue('C1', 'Phân Khu');
        $sheet->setCellValue('D1', 'Ngày chốt');
        $sheet->setCellValue('E1', 'Chỉ số chốt');
        $sheet->setCellValue('F1', 'Phí của tháng');
        $apartments = ApartmentMapResidentUser::find()->where(['building_cluster_id' => 1, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all();
        $i = 2;
        foreach ($apartments as $apartment) {
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $apartment->apartment_name);
            $sheet->setCellValue('C' . $i, trim($apartment->apartment_parent_path, '/'));
//            $sheet->setCellValue('D'.$i, "'".date('d/m/Y'));
            $sheet->setCellValue('D' . $i, date('d/m/Y'));
            $sheet->getStyle('D' . $i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
            $sheet->setCellValue('E' . $i, 0);
//            $sheet->setCellValue('F'.$i, "'".date('d/m/Y'));
            $sheet->setCellValue('F' . $i, date('d/m/Y'));
            $sheet->getStyle('F' . $i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        die;
    }

    public function actionCreatePdf($file_path = null)
    {

        $this->layout = 'print';
        $apartment = Apartment::findOne(['id' => 9]);
        $content_html = $this->render('/pdf/fee_new', ['apartment' => $apartment]);


//        $html = <<<HTML
//<html xmlns="http://www.w3.org/1999/xhtml">
//<head>
//    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
//</head>
//<body>
//<p>Simple Content</p>
//</body>
//</html>
//HTML;
//        Yii::$app->html2pdf
//            ->convert($html)
//            ->saveAs(dirname((dirname(__DIR__))) . "/db_desgin/" . time() . '-test.pdf');
//        die;


        if ($file_path == null) {
//            $file_path = '/uploads/applications/201907/'.time().'-test_nuoc.xlsx';
            $file_path = $fxls = dirname((dirname(__DIR__))) . "/db_desgin/" . time() . '-test.pdf';
        }
        $date = date("dmY");
        $pdf = Yii::$app->pdf;
        // or new Pdf();
        $mpdf = $pdf->api;
        // fetches mpdf api
//        $mpdf->shrink_tables_to_fit = 8;
//        $mpdf->use_kwt = true;
//        $mpdf->table_keep_together = true;
        $mpdf->WriteHtml($content_html);
        // call mpdf write html
        $filename = dirname((dirname(__DIR__))) . "/db_desgin/" . time() . '-test.pdf';
        $mpdf->Output($filename, 'F');
        // call the mpdf api output as needed
        $pdf->render();
        echo $filename;
        die;
    }
}
