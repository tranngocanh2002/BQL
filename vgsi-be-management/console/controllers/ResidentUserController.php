<?php

namespace console\controllers;

use common\helpers\ApiHelper;
use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\PaymentGenCodeItem;
use common\models\ResidentUser;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityConfig;
use common\models\ResidentUserAccessToken;
use common\models\ResidentUserDeviceToken;
use Exception;
use Yii;
use yii\console\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;


class ResidentUserController extends Controller
{
    /*
     * Hệ thống delete user sau 15 ngày báo xóa
     * chạy định kỳ cuối mỗi ngày /1 lần
     * Check những user có thời gian báo xóa nhỏ hơn thời gian hiện tại trừ 15 ngày thì xóa
    */
    function actionDeleteEndDay()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            echo "Push Start: " . date('Y-m-d H:i:s', time()) . "\n";
            $residentUsers = ResidentUser::find()
                ->where(['is_deleted' => ResidentUser::NOT_DELETED])
                ->andWhere(['NOT', ['deleted_at' => null]])
                ->andWhere(['<=', 'deleted_at', time() - 90])
                ->all();
            foreach ($residentUsers as $residentUser) {
                echo $residentUser->is_deleted . "\n";
                $residentUser->is_deleted = ResidentUser::DELETED;
                $residentUser->save();
                //Hủy đang nhập trên các thiết bị khác
                ResidentUserAccessToken::deleteAll(['resident_user_id' => $residentUser->id]);
                ResidentUserDeviceToken::deleteAll(['resident_user_id' => $residentUser->id]);
            }
            $transaction->commit();
            echo 'End Delete: '. date('Y-m-d H:i:s', time()) . "\n";
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            print_r($ex->getMessage());
            echo "error\n";
        }
    }
}