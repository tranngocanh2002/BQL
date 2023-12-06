<?php

namespace console\controllers;

use common\helpers\QueueLib;
use common\models\Apartment;
use common\models\BuildingArea;
use common\models\BuildingCluster;
use common\models\ManagementUser;
use common\models\Request;
use common\models\ServiceMapManagement;
use common\models\ServicePaymentFee;
use Exception;
use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;

class MailController extends Controller
{
    public function actionSendActive($building_id, $user_id)
    {
        $buildingCluster = BuildingCluster::findOne(['id' => $building_id]);
        $user = ManagementUser::findOne($user_id);
        $domain = (!empty($buildingCluster)) ? $buildingCluster->domain : '';
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'activeAccount-html'],
                ['user' => $user, 'linkWeb' => $domain . '/user/createpassword?token=faserqwejfwerqewrqfasdf']
            )
            ->setFrom([Yii::$app->params['supportEmail'] => $buildingCluster->name])
            ->setTo($user->email)
            ->setSubject('Kích hoạt tài khoản')
            ->send();
    }

    public function actionWelcome($building_id, $user_id)
    {
        $buildingCluster = BuildingCluster::findOne(['id' => $building_id]);
        $user = ManagementUser::findOne($user_id);
        $domain = (!empty($buildingCluster)) ? $buildingCluster->domain : '';
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'welcome-html'],
                ['user' => $user, 'linkWeb' => $domain]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => $buildingCluster->name])
            ->setTo($user->email)
            ->setSubject('Chào mừng gia nhập hệ thống quản lý '.$buildingCluster->name)
            ->send();
    }

    public function actionResetPassword($building_id, $user_id)
    {
        $buildingCluster = BuildingCluster::findOne(['id' => $building_id]);
        $user = ManagementUser::findOne($user_id);
        $domain = (!empty($buildingCluster)) ? $buildingCluster->domain : '';
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'forgotPassword-html'],
                ['user' => $user, 'linkWeb' => $domain . '/user/createpassword?token=faserqwejfwerqewrqfasdf']
            )
            ->setFrom([Yii::$app->params['supportEmail'] => $buildingCluster->name])
            ->setTo($user->email)
            ->setSubject('['.$buildingCluster->name.']Thay đổi mật khẩu')
            ->send();
    }

    public function actionNotifyRequest($building_id, $user_id, $request_id)
    {
        $buildingCluster = BuildingCluster::findOne(['id' => $building_id]);
        $user = ManagementUser::findOne($user_id);
        $request = Request::findOne($request_id);
        $domain = (!empty($buildingCluster)) ? $buildingCluster->domain : '';
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'notificationRequest-html'],
                ['user' => $user, 'request' => $request,'linkWeb' => $domain . '/user/createpassword?token=faserqwejfwerqewrqfasdf']
            )
            ->setFrom([Yii::$app->params['supportEmail'] => $buildingCluster->name])
            ->setTo($user->email)
            ->setSubject('[Yêu cầu] '. $request->title)
            ->send();
    }

    public function actionSendAws($email = 'lucnn@luci.vn')
    {
        $email_tos = [$email];
        //gửi email ở đây
        $aws = Yii::$app->params['aws'];
        $subject = 'test send email';
        $contentHtml = 'xin chao! email to aws';
        if (!empty($aws) && !empty($email_tos)) {
            $payload = [
                'sender' => $aws['sender'],
                'aws_config' => $aws['config'],
                'subject' => $subject,
                'to' => $email_tos,
                'content' => $contentHtml,
            ];
            QueueLib::channelEmailAws(json_encode($payload));
        }
        //end gửi email ở đây
    }
}