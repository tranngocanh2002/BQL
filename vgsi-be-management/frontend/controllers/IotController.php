<?php

namespace frontend\controllers;

use common\helpers\ApiHelper;
use common\helpers\MsgCode;
use common\helpers\NotificationTemplate;
use common\helpers\OneSignalApi;
use common\helpers\QueueLib;
use common\helpers\SocketHelper;
use common\models\AnnouncementCampaign;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingCluster;
use common\models\Campaign;
use common\models\CampaignPush;
use common\models\CampaignQueue;
use common\models\DeviceStatus;
use common\models\DeviceType;
use common\models\Hotel;
use common\models\ManagementUser;
use common\models\ManagementUserNotify;
use common\models\MapDeviceApp;
use common\models\NotifyPush;
use common\models\Operator;
use common\models\OperatorMapHotel;
use common\models\Device;
use common\models\ResidentUserNotify;
use common\models\RuleKeyCard;
use frontend\models\chart\ChartKeyCard;
use Yii;
use common\helpers\ErrorCode;
use yii\web\HttpException;
use console\controllers\QueueRuleKeyCard;
use common\models\JobKeyCard;
use frontend\models\KeyCardControlForm;

/**
 * Nhập bản tin từ local: iot->backend->app/web
 * Class IotController
 * @package frontend\controllers
 */
class IotController extends ApiSystemController {

    public function verbs() {
        return [
            'receive-status' => ['POST'],
        ];
    }

    public function actionDeviceConnectLumi() {
        $payload = Yii::$app->request->post('payload', '');
        Yii::warning($payload);
        //xử lý phần kết nối demo đèn đường
        $socket = new SocketHelper();
        $socket->of('/')->to(['lighting_lumi'])->flag('broadcast')->emit('status', ['payload' => $payload]);
        Yii::info($socket->res());
        return [
            'success' => true,
            'message' => Yii::t('app', "Success"),
        ];
    }

    public function actionReceiveStatusLumi() {
        $payload = Yii::$app->request->post('payload', '');
        Yii::warning($payload);
        //xử lý phần kết nối demo đèn đường
        $socket = new SocketHelper();
        $socket->of('/')->to(['lighting_lumi'])->flag('broadcast')->emit('status', ['payload' => $payload]);
        Yii::info($socket->res());
        return [
            'success' => true,
            'message' => Yii::t('app', "Success"),
        ];
    }

    public function actionReceiveStatus() {
        $payload = Yii::$app->request->post('payload', '');
//        {"cmd": "status", "reqid": "string", "objects": [
//         {"type": "devices",  "data": [
//                        {"device_id": "string", "status": 1, "states": {"OnOff": {"on":true}, "StartStop": {"start": false} }},
//                        {"device_id": "string", "status": 0, "errorCode": "code", "errorMessage": "string"}
//                ]
//         }
//       ]}
        $lrn = Yii::$app->request->post('lrn', '');
        Yii::info($payload);

        if (!$payload || !$lrn) {
            return [
                'success' => false,
                'message' => Yii::t('app', MsgCode::get(ErrorCode::ERROR_INVALID_PARAM)),
                'statusCode' => ErrorCode::ERROR_NOT_FOUND
            ];
        }

        $is_send = false;
        if(!empty($payload['objects']) && is_array($payload['objects'])){
            foreach ($payload['objects'] as $object){
                if(!empty($object['type']) && $object['type'] == 'devices'){
                    if(!empty($object['data']) && is_array($object['data'])){
                        foreach ($object['data'] as $data){
                            if($data['device_id'] == 'PCCC0001_$$_4' && $data['states']['FireAlarm']['fireAlarm'] == 1){
                                $is_send = true;
                            }
                        }
                    }
                }
            }
        }
        /*
         * chuyển tiếp bản tin xuống WEB/APP
         * TODO: Tạm thời gửi cho tất cả các cluster, sau update cơ ché sync device thì update ở đây
         */
        $buildingClusters = BuildingCluster::find()->where(['is_deleted' => BuildingCluster::NOT_DELETED])->all();
        $rooms = [];
        $buildingCluster = null;
        foreach ($buildingClusters as $buildingClusterSocket){
            $rooms[] = 'building_cluster_' . $buildingClusterSocket->id;
            if($buildingClusterSocket->id == 6){
                $buildingCluster = $buildingClusterSocket;
            }
        }

        $socket = new SocketHelper();
        $socket->of('/')->to($rooms)->flag('broadcast')->emit('status', ['payload' => $payload]);
        Yii::info($socket->res());
        /*
         * TODO: cố định building cluster id gửi thông báo qua sms là 6
         */
        if($is_send == true){
            $oneSignalApi = new OneSignalApi();
            //gửi thông báo notify tới web ban quản lý
            $managementUsers = ManagementUser::find()->where(['building_cluster_id' => $buildingCluster->id])->all();
//        $title = 'Cảnh báo có người lạ khu vực sảnh A1 luc';
            $title = 'Cảnh báo có cháy tại tòa nhà ZEN TOWER Vui lòng sơ tán khẩn cấp ' . date('H:i:s d/m/Y', time());
            $description = $title;
            $data = [
                'type' => 'mode',
                'action' => 'security_mode',
                'management_user_id' => 0
            ];
            $app_id = $buildingCluster->one_signal_app_id;

            $typeNotify = ManagementUserNotify::TYPE_SECURITY_MODE;
            foreach ($managementUsers as $managementUser){
                //khởi tạo log cho từng management user
                $managementUserNotify = new ManagementUserNotify();
                $managementUserNotify->building_cluster_id = $buildingCluster->id;
                $managementUserNotify->management_user_id = $managementUser->id;
                $managementUserNotify->type = $typeNotify;
                $managementUserNotify->title = $title;
                $managementUserNotify->description = $description;
                if (!$managementUserNotify->save()) {
                    Yii::error($managementUserNotify->getErrors());
                }
                //end log

                //gửi thông báo theo device token
                $player_ids = [];
                foreach ($managementUser->managementUserDeviceTokens as $managementUserDeviceToken) {
                    $player_ids[] = $managementUserDeviceToken->device_token;
                }
                $data['management_user_id'] = $managementUser->id;
                $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title, $description, $player_ids, $data, null, $app_id);
                //end gửi thông báo theo device token
            }

            //Gủi thông báo cho cư dân
            $player_ids = [];
            $content = $title;
            $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['building_cluster_id' => $buildingCluster->id])->all();
            foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser) {
                //khởi tạo log cho từng resident user
                $residentUserNotify = new ResidentUserNotify();
                $residentUserNotify->building_cluster_id = $buildingCluster->id;
                $residentUserNotify->building_area_id = $apartmentMapResidentUser->building_area_id;
                $residentUserNotify->resident_user_id = $apartmentMapResidentUser->resident->id ?? null;
                $residentUserNotify->type = ResidentUserNotify::TYPE_ANNOUNCEMENT;
                $residentUserNotify->title = $title;
                $residentUserNotify->description = $content;
                if (!$residentUserNotify->save()) {
                    print_r($residentUserNotify->getErrors());
                }
                //end log

                //lấy thông tin device token cần gửi
                if (!empty($apartmentMapResidentUser->residentUserDeviceTokens)) {
                    $residentUserDeviceTokens = $apartmentMapResidentUser->residentUserDeviceTokens;
                    foreach ($residentUserDeviceTokens as $residentUserDeviceToken) {
                        $player_ids[] = $residentUserDeviceToken->device_token;
                    }
                }
            }
            //gửi thông báo theo device token
            $data = [];
            $oneSignalApi->sendToWorkerPlayerIds($title, $content, $player_ids, $data, null, null);


            //gửi tin nhắn tới thanh viên ban quản lý
            if($buildingCluster->security_mode === BuildingCluster::SECURITY_MODE){
                $PhoneWhiteList = Yii::$app->params['PhoneWhiteList'];
                $SmsCmc = Yii::$app->params['SmsCmc'];
                $contentSms = $title;
                $payload = [
                    'to' => '',
                    'utf' => true,
                    'content' => $contentSms
                ];
                $payload = array_merge($payload, $SmsCmc);

                foreach ($managementUsers as $managementUser){
                    if(in_array($managementUser->phone, $PhoneWhiteList)){
                        $payload['to'] = $managementUser->phone;
                        QueueLib::channelSms(json_encode($payload), true);
                    }
                }
            }
        }

        return [
            'success' => true,
            'message' => 'ok',
            'statusCode' => 200,
            'socketRes' => $socket->res()
        ];
    }
}
