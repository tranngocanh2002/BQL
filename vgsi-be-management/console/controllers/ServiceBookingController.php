<?php

namespace console\controllers;

use common\helpers\ApiHelper;
use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\PaymentGenCodeItem;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityConfig;
use Exception;
use Yii;
use yii\console\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;


class ServiceBookingController extends Controller
{

    /*
    * Hệ thống báo book đến thời gian sử dụng
    * chạy định kỳ 1 phút /1 lần
    * Check những booking hết thời gian chờ tạo yêu cầu thanh toán thì hủy
    * $s : số luồng chạy đông thời
   * $t : số dư phép chia mỗi luồng
   */
    function actionCancelDelayPayRequest($s, $t)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            echo 'Start Cancel' . "\n";
            $query = ServiceUtilityBooking::find()
                ->where(['status' => ServiceUtilityBooking::STATUS_CREATE])
                ->andWhere("id%$s=$t");
            $c = 0;
            foreach ($query->each() as $booking) {
                echo $booking->id . "\n";
                //check thời gian chờ tạo yêu cầu
                if(!$booking->serviceUtilityConfig){
                    echo 'Không tồn tại cấu hình dịch vụ' . "\n";
                }else{
                    if($booking->created_at < (time() - $booking->serviceUtilityFree->timeout_pay_request * 60)){
                        if(empty($booking->service_payment_fee_id)){
                            if($booking->cancelBook(ServiceUtilityBooking::STATUS_CANCEL_SYSTEM)){
                                $c++;
                            }
                        }else{
                            $paymentGenCodeItem = PaymentGenCodeItem::findOne(['service_payment_fee_id' => $booking->service_payment_fee_id]);
                            if(empty($paymentGenCodeItem)){
                                if($booking->cancelBook(ServiceUtilityBooking::STATUS_CANCEL_SYSTEM)){
                                    $c++;
                                }
                            }
                        }
                    }
                }
            }
            $transaction->commit();
            echo 'Total Cancel: ' . $c . "\n";
            echo 'End Cancel' . "\n";
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            print_r($ex->getMessage());
            echo "error\n";
        }
    }

    /*
     * Hệ thống hủy book hết hạn chờ tạo yêu cầu thanh toán
     * chạy định kỳ 1 phút /1 lần
     * thông báo trước thời gian sử dụng 60p
     * $s : số luồng chạy đông thời
    * $t : số dư phép chia mỗi luồng
    */
    function actionBeforeNotify($s, $t)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            echo 'Start Before' . "\n";
            $time_current = time();
            $query = ServiceUtilityBooking::find()
                ->where(['status' => ServiceUtilityBooking::STATUS_ACTIVE, 'is_send_notify' => ServiceUtilityBooking::IS_NOT_SEND_NOTIFY])
                ->andWhere(['<=', 'start_time', $time_current + 60*60])
                ->andWhere("id%$s=$t");
            $c = 0;
            foreach ($query->each() as $booking) {
                /* @var $booking ServiceUtilityBooking */
                echo $booking->id . "\n";
                //check thời gian chờ tạo yêu cầu
                $booking->sendNotifyToResidentUser($managementUserIgnore = null, $residentUserIgnore = null, $is_create = ServiceUtilityBooking::SEND_NOTIFY);
                $booking->is_send_notify = ServiceUtilityBooking::IS_SEND_NOTIFY;
                if(!$booking->save()){
                    Yii::error($booking->getMessage());
                }
            }
            $transaction->commit();
            echo 'Total Before: ' . $c . "\n";
            echo 'End Before' . "\n";
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            print_r($ex->getMessage());
            echo "error\n";
        }
    }

    /*
     * Hệ thống hủy book hết hạn cuối ngày
     * chạy định kỳ cuối mỗi ngày /1 lần
     * Check những booking có thời gian sử dụng nhỏ hơn thời gian cuối ngày hiện tại thì hủy
     * $s : số luồng chạy đông thời
    * $t : số dư phép chia mỗi luồng
    */
    function actionCancelEndDay($s, $t)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            echo 'Start Cancel' . "\n";
            $query = ServiceUtilityBooking::find()
                ->where(['status' => ServiceUtilityBooking::STATUS_CREATE])
                ->andWhere(['<=', 'start_time', time()])
                ->andWhere("id%$s=$t");
            $ct = 0;
            $cs = 0;
            foreach ($query->each() as $booking) {
                echo $booking->id . "\n";
                if($booking->cancelBook(ServiceUtilityBooking::STATUS_CANCEL_SYSTEM)){
                    $cs++;
                }else {
                    echo 'Huy loi' . "\n";
                }
                $ct++;
            }
            $transaction->commit();
            echo 'Total Cancel: ' . $ct . "\n";
            echo 'Total success: ' . $cs. "\n";
            echo 'End Cancel' . "\n";
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            print_r($ex->getMessage());
            echo "error\n";
        }
    }
}