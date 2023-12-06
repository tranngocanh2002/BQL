<?php

namespace console\controllers;

use common\helpers\ApiHelper;
use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\helpers\NotificationTemplate;
use common\helpers\OneSignalApi;
use common\helpers\QueueLib;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementItem;
use common\models\AnnouncementItemSend;
use common\models\AnnouncementSurvey;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ManagementUser;
use common\models\PaymentGenCode;
use common\models\PaymentGenCodeItem;
use common\models\ResidentUser;
use common\models\ResidentUserDeviceToken;
use common\models\ResidentUserMapRead;
use common\models\ResidentUserNotify;
use common\models\ServiceBill;
use common\models\ServiceDebt;
use common\models\ServiceParkingFee;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityBooking;
use common\models\Job;
use console\models\AnnouncementCampaignSearch;
use Exception;
use frontend\models\ServicePaymentFeeResponse;
use frontend\models\AnnouncementItemSearch;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;
use yii\swiftmailer\Mailer;


class AnnouncementController extends Controller
{
    /*
     * gửi thông báo
     * Chạy định kỳ 1 phút / 1 lần
     * chia ra nhiều luồng để chạy
     * $s : số luồng chạy đồng thời
     * $t : số dư phép chia mỗi luồng
     */
    
    public function actionPush($s, $t)
    {
        echo "Push Start: " . date('Y-m-d H:i:s', time()) . "\n";
        // gửi thông báo cho tất cả các bảng tin được tạo từ web qltt với status = 2 và type = -1
        $querys = AnnouncementCampaign::find()
            ->where([
                'type' => AnnouncementCampaign::TYPE_POST_NEW,
                'is_send_event' => 0,
            ])
            ->andWhere(['status' => AnnouncementCampaign::STATUS_PUBLIC_AT])
            // ->andWhere(['LIKE', 'targets', ["0"]])
            ->andWhere(['<', 'send_event_at', time()])
            ->andWhere("id%$s=$t")
            ->orderBy(['created_at' => SORT_DESC])->all();
        foreach($querys as $query)
        {
            $query->status = AnnouncementCampaign::STATUS_ACTIVE;
            $query->is_send = AnnouncementCampaign::IS_SEND;
            $query->save();            
            $query->sendNotifyToResidentUser($query->title,$query->title_en,json_decode($query->targets),$query->id);
        }
        $campaigns = AnnouncementCampaign::find()
            ->where(['is_send' => AnnouncementCampaign::IS_UNSEND, 'status' => AnnouncementCampaign::STATUS_ACTIVE])
            ->andWhere(['>=', 'type', AnnouncementCampaign::TYPE_DEFAULT])
            ->andWhere(['<', 'send_at', time()])
            ->andWhere(['is_event' => AnnouncementCampaign::IS_NOT_EVENT])
            ->andWhere("id%$s=$t");
        foreach ($campaigns->each() as $campaign) {
            $isCheckSendEmail = [] ;
            $announcementCategory = $campaign->announcementCategory;
            $buildingCluster = $campaign->buildingCluster;
            $itemSends = AnnouncementItemSend::find()->where(['announcement_campaign_id' => $campaign->id])->all();
            $campaign->is_send = AnnouncementCampaign::IS_SEND;
            if (!$campaign->save()) {
                print_r($campaign->getErrors());
                continue;
            }
            $apartment_not_send_ids = [];
            $count = 0;
            if (!empty($campaign->apartment_not_send_ids)) {
                $apartment_not_send_ids = Json::decode($campaign->apartment_not_send_ids, true);
            }
            foreach ($itemSends as $itemSend) {

                //khởi tạo log item cho từng căn hộ
                if (empty($itemSend->apartment_id)) {
                    $apartments = Apartment::find()->where(['building_area_id' => $itemSend->building_area_id, 'is_deleted' => Apartment::NOT_DELETED]);
                    if (!empty($apartment_not_send_ids)) {
                        $apartments = $apartments->andWhere(['not', ['id' => $apartment_not_send_ids]]);
                    }
                    $apartments = $apartments->all();
                    foreach ($apartments as $apartment) {
                        $campaign->total_apartment_send++;
                        // $res_send = self::sendToApartment($campaign, $announcementCategory, $buildingCluster, $itemSend, $apartment);
                        $res_send = self::sendToApartmentNew($campaign, $announcementCategory, $buildingCluster, $itemSend, $apartment,$isCheckSendEmail,$count);
                        print_r($res_send);
                        if (!empty($res_send['is_send_email'])) {
                            $campaign->total_email_send++;
                        }
                        if (!empty($res_send['is_send_sms'])) {
                            $campaign->total_sms_send++;
                        }
                        if (!empty($res_send['is_send_app'])) {
                            $campaign->total_app_send++;
                        }
                    }
                } else {
                    $apartment = Apartment::findOne(['id' => $itemSend->apartment_id, 'is_deleted' => Apartment::NOT_DELETED]);
                    $campaign->total_apartment_send++;
                    // $res_send = self::sendToApartment($campaign, $announcementCategory, $buildingCluster, $itemSend, $apartment);
                    $res_send = self::sendToApartmentNew($campaign, $announcementCategory, $buildingCluster, $itemSend, $apartment,$isCheckSendEmail,$count);
                    if (!empty($res_send['is_send_email'])) {
                        $campaign->total_email_send++;
                    }
                    if (!empty($res_send['is_send_sms'])) {
                        $campaign->total_sms_send++;
                    }
                    if (!empty($res_send['is_send_app'])) {
                        $campaign->total_app_send++;
                    }
                }
            }
            if(!empty($campaign->add_email_send) || !empty($campaign->add_phone_send)){
                $res_send_to_append = self::sendToAppendEmailAndPhone($campaign, $announcementCategory, null, $buildingCluster);
                if(!empty($campaign->add_email_send)){
                    $campaign->total_email_send += count(Json::decode($campaign->add_email_send, true));
                }
                if(!empty($campaign->add_phone_send)){
                    $campaign->total_sms_send += count(Json::decode($campaign->add_phone_send, true));
                }
            }
            $campaign->total_app_success = $campaign->total_app_send;
            if (!$campaign->save()) {
                print_r($campaign->getErrors());
            }
            /*
             * Gửi email thông báo có phí cần duyệt
             * gửi thông báo cho những management_user được quyền nhận
             * vì đang chạy nhiều luồng gưi thông báo nên phải xử lý chỉ gửi thông báo 1 lần
             */
                    //            if (($campaign->total_email_send > 0 || $campaign->total_sms_send > 0 || $campaign->total_app_send > 0) && !empty($buildingCluster->setting_group_receives_notices_financial)) {
                    //                $notices_ids = json_decode($buildingCluster->setting_group_receives_notices_financial, true);
                    //                $managementUsers = ManagementUser::find()->where(['id' => $notices_ids, 'is_deleted' => ManagementUser::NOT_DELETED, 'status' => ManagementUser::STATUS_ACTIVE])->all();
                    //                foreach ($managementUsers as $managementUser) {
                    //                    $managementUser->activeFeeNotices();
                    //                }
                    //            }
            
            if ($campaign->is_send_email == AnnouncementCampaign::IS_SEND_EMAIL) {
                foreach ($isCheckSendEmail as $isCheckSendEmails) {
                    self::sendEmailToUserInHere($isCheckSendEmails);
                }
            }
        }
        //gửi email đến user ở đây;
        echo "Push End: " . date('Y-m-d H:i:s', time()) . "\n";
    }

    public function actionPushIsEvent($s, $t)
    {
        echo "Push Is Event Start\n";
        $timeSend = time() + 24 * 60 * 60; // gửi thông báo nhắc trước 1 ngày
        // ké luồng gửi thông báo công việc
        $querys = Job::find()->where(['status' => Job::CREATE,'flag_check_send_reminder'=> 0 ])->andWhere(['<','time_end',$timeSend])->all();
        $data = [];
        foreach($querys as $key => $query)
        {
            $data[$key]['performer'] = $query->performer ?? "";
            $data[$key]['title']     = $query->title ?? "";
            $query->flag_check_send_reminder = 1;
            $query->save();
        }
        foreach ($data as $item) {
            if (isset($item["title"]) && isset($item["performer"])) {
                $arrPerformer = explode(',', $item['performer']);
                $query->sendNotifyToPerformer(Job::REMIND_WORK, $arrPerformer,$item['title']);
                // $arrPeopleInvolved = explode(',', $query->people_involved);
                // $query->sendNotifyToPeopleInvolved(Job::CREATE, $arrPeopleInvolved);
            }
        }
        $campaigns = AnnouncementCampaign::find()
            ->where(['is_event' => AnnouncementCampaign::IS_EVENT, 'is_send_event' => AnnouncementCampaign::IS_UNSEND_EVENT, 'status' => AnnouncementCampaign::STATUS_ACTIVE])
            ->andWhere(['>=', 'type', AnnouncementCampaign::TYPE_DEFAULT])
            ->andWhere(['<', 'send_event_at', $timeSend])
            ->andWhere("id%$s=$t");
        foreach ($campaigns->each() as $campaign) {
            $announcementCategory = $campaign->announcementCategory;
            $buildingCluster = $campaign->buildingCluster;
            $itemSends = AnnouncementItemSend::find()->where(['announcement_campaign_id' => $campaign->id])->all();
            $campaign->is_send_event = AnnouncementCampaign::IS_SEND_EVENT;
            if (!$campaign->save()) {
                print_r($campaign->getErrors());
            }
            foreach ($itemSends as $itemSend) {
                //khởi tạo log item cho từng căn hộ
                if (empty($itemSend->apartment_id)) {
                    $apartments = Apartment::find()->where(['building_area_id' => $itemSend->building_area_id, 'is_deleted' => Apartment::NOT_DELETED])->all();
                    foreach ($apartments as $apartment) {
                        $campaign->total_apartment_send++;
//                        $res_send = self::sendToApartment($campaign, $announcementCategory, $buildingCluster, $itemSend, $apartment);
                        $res_send = self::sendToApartmentNew($campaign, $announcementCategory, $buildingCluster, $itemSend, $apartment);
                        if (!empty($res_send['is_send_email'])) {
                            $campaign->total_email_send++;
                        }
                        if (!empty($res_send['is_send_sms'])) {
                            $campaign->total_sms_send++;
                        }
                        if (!empty($res_send['is_send_app'])) {
                            $campaign->total_app_send++;
                        }
                    }
                } else {
                    $apartment = Apartment::findOne(['id' => $itemSend->apartment_id]);
                    $campaign->total_apartment_send++;
//                    $res_send = self::sendToApartment($campaign, $announcementCategory, $buildingCluster, $itemSend, $apartment);
                    $res_send = self::sendToApartmentNew($campaign, $announcementCategory, $buildingCluster, $itemSend, $apartment);
                    if (!empty($res_send['is_send_email'])) {
                        $campaign->total_email_send++;
                    }
                    if (!empty($res_send['is_send_sms'])) {
                        $campaign->total_sms_send++;
                    }
                    if (!empty($res_send['is_send_app'])) {
                        $campaign->total_app_send++;
                    }
                }
            }

            if(!empty($campaign->add_email_send) || !empty($campaign->add_phone_send)){
                $res_send_to_append = self::sendToAppendEmailAndPhone($campaign, $announcementCategory, null, $buildingCluster);
                print_r($res_send_to_append);
                if(!empty($campaign->add_email_send)){
                    $campaign->total_email_send += count(Json::decode($campaign->add_email_send, true));
                }
                if(!empty($campaign->add_phone_send)){
                    $campaign->total_sms_send += count(Json::decode($campaign->add_phone_send, true));
                }
            }

            $campaign->total_app_success = $campaign->total_app_send;
            if (!$campaign->save()) {
                print_r($campaign->getErrors());
            }
        }
        echo "Push Is Event End\n";
    }

    public function sendToApartment($announcementCampaign, $announcementCategory, $buildingCluster, $announcementItemSend, $apartment)
    {
        $link_api = Yii::$app->params['link_api'];
        $HeaderKey = Yii::$app->params['HeaderKey'];
        $SmsCmc = Yii::$app->params['SmsCmc'];
        //check thong tin building cluster có tài khoản gửi sms thì dùng
        if (!empty($buildingCluster->sms_account_push) && !empty($buildingCluster->sms_password_push) && !empty($buildingCluster->sms_brandname_push)) {
            $SmsCmc = [
                'Brandname' => $buildingCluster->sms_brandname_push,
                'user' => $buildingCluster->sms_account_push,
                'pass' => $buildingCluster->sms_password_push,
            ];
        }
        $domain_link = $link_api['web'];
        $oneSignalApi = new OneSignalApi();
        $announcementItem = new AnnouncementItem();
        $announcementItem->announcement_campaign_id = $announcementCampaign->id;
        $announcementItem->building_cluster_id = $announcementCampaign->building_cluster_id;
        $announcementItem->building_area_id = $announcementItemSend->building_area_id;
        $announcementItem->announcement_item_send_id = $announcementItemSend->id;
        $announcementItem->end_debt = $announcementItemSend->end_debt;
        $announcementItem->type = $announcementItemSend->type;
        $announcementItem->apartment_id = $apartment->id;
        $announcementItem->resident_user_name = $apartment->resident_user_name;
        $announcementItem->description = $announcementCampaign->description;
        $announcementItem->content = $announcementCampaign->content;
        $announcementItem->content_sms = $announcementCampaign->content_sms;
        $announcementItem->status_notify = AnnouncementItem::STATUS_SUCCESS; // mặc định là thành công

        $attachments = [];
        $content_pdf = [];
        $is_send_app = 0;
        $is_send_email = 0;
        $is_send_sms = 0;
        $is_reminder_debt = 0;
        if (in_array($announcementCampaign->type, [AnnouncementCampaign::TYPE_REMINDER_DEBT_1, AnnouncementCampaign::TYPE_REMINDER_DEBT_2, AnnouncementCampaign::TYPE_REMINDER_DEBT_3, AnnouncementCampaign::TYPE_REMINDER_DEBT_4, AnnouncementCampaign::TYPE_REMINDER_DEBT_5])) {
            $is_reminder_debt = 1;

            //tạm thời không tạo file
//            $name_file = 'payment_fee_' . date('dmY', time()) . '_' . $apartment->id . '.pdf';
//            $content_pdf = [
//                'name' => $name_file,
//                'data' => $domain_link . '/read/pdf?apartment_id=' . $apartment->id . '&campaign_type=' . $announcementCampaign->type
//            ];
        }

        $announcementItem->content = self::genContent($announcementCampaign, $apartment, $announcementItemSend);
        $announcementItem->description = CVietnameseTools::truncateString(strip_tags($announcementItem->content), 100);
        $announcementItem->content_sms = self::getContentSms($announcementCampaign, $apartment, $announcementItemSend);
        if (!$announcementItem->save()) {
            print_r($announcementItem->getErrors());
        }

        $callback = [
            'id' => $announcementItem->id,
            'url' => '',
            'api_key_name' => $HeaderKey['HEADER_API_KEY'],
            'api_key_value' => $HeaderKey['API_KEY_WEB'],
        ];

        $player_ids = [];
        $title = $title_en = NotificationTemplate::vsprintf(NotificationTemplate::NOTIFICATION_NEW_TO_RESIDENT, [$announcementCampaign->title]);
        $content = $content_en = $announcementCampaign->title;
        $residentUserIds = []; // user cần update trạng thái thông báo là chưa đọc
        $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['apartment_id' => $apartment->id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all();
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser) {
            //khởi tạo log cho từng resident user
            $residentUserNotify = new ResidentUserNotify();
            $residentUserNotify->building_cluster_id = $announcementItem->building_cluster_id;
            $residentUserNotify->building_area_id = $announcementItem->building_area_id;
            $residentUserNotify->resident_user_id = $apartmentMapResidentUser->residen->id ?? null;
            $residentUserNotify->type = ResidentUserNotify::TYPE_ANNOUNCEMENT;
            $residentUserNotify->announcement_item_id = $announcementItem->id;
            $residentUserNotify->title = $title;
            $residentUserNotify->description = $content;
            if (!$residentUserNotify->save()) {
                print_r($residentUserNotify->getErrors());
            }
            //end log

            //lấy thông tin device token cần gửi
            $device_token = [];
//            if ($apartmentMapResidentUser->resident_user_is_send_notify == ApartmentMapResidentUser::IS_SEND_NOTIFY) {
            $residentUserIds[] = $apartmentMapResidentUser->resident->id ?? null;
            if (!empty($apartmentMapResidentUser->residentUserDeviceTokens)) {
                $residentUserDeviceTokens = $apartmentMapResidentUser->residentUserDeviceTokens;
                foreach ($residentUserDeviceTokens as $residentUserDeviceToken) {
                    $device_token[] = $residentUserDeviceToken->device_token;
                    $player_ids[] = $residentUserDeviceToken->device_token;
                }
            }
//            }
            if (!empty($device_token) && $apartmentMapResidentUser->type == ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD) {
                $is_send_app = 1;
            }
//            $player_ids = array_merge($player_ids, $device_token);
        }
        if ($announcementCampaign->is_send_push == AnnouncementCampaign::IS_SEND_PUSH) {
            //gửi thông báo theo device token
            $data = [
                'type' => 'notify',
                'notify_id' => $announcementItem->id,
            ];
//            $player_ids = array_unique($player_ids);
            print_r($player_ids);
            $callback['url'] = $domain_link . '/callback/notify';
            $oneSignalApi->sendToWorkerPlayerIds($title, $content, $title_en, $content_en, $player_ids, $data, null, null, $callback);
            //end gửi thông báo theo device token
            $announcementItem->device_token = implode(',', $player_ids);
        }

//        if ($announcementCampaign->is_send_email == AnnouncementCampaign::IS_SEND_EMAIL) {
        $contentHtml = $announcementItem->content;

        //nếu là email nhắc nợ thì check gen code để add link thanh toán online vào email luôn
        if ($is_reminder_debt == 1) {
            $paymentGenCode = PaymentGenCode::findOne(['apartment_id' => $apartment->id, 'is_auto' => PaymentGenCode::IS_AUTO, 'status' => PaymentGenCode::STATUS_UNPAID]);
            if (empty($paymentGenCode)) {
                $paymentGenCode = new PaymentGenCode();
                $paymentGenCode->building_cluster_id = $apartment->building_cluster_id;
                $paymentGenCode->apartment_id = $apartment->id;
                $paymentGenCode->is_auto = PaymentGenCode::IS_AUTO;
                $paymentGenCode->status = PaymentGenCode::STATUS_UNPAID;
                $paymentGenCode->type = PaymentGenCode::PAY_ONLINE;
                $paymentGenCode->generateCode();
                if (!$paymentGenCode->save()) {
                    print_r($paymentGenCode->errors);
                }
            }
            //lấy các item mới nhất
            PaymentGenCodeItem::deleteAll(['payment_gen_code_id' => $paymentGenCode->id, 'status' => PaymentGenCodeItem::STATUS_UNPAID]);
            $servicePaymentFees = ServicePaymentFee::find()->where(['building_cluster_id' => $paymentGenCode->building_cluster_id, 'apartment_id' => $paymentGenCode->apartment_id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT, 'is_debt' => ServicePaymentFee::IS_DEBT])
                ->andWhere(['>', 'more_money_collecte', 0])->all();
            foreach ($servicePaymentFees as $servicePaymentFee) {
                $check = PaymentGenCodeItem::findOne(['service_payment_fee_id' => $servicePaymentFee->id, 'status' => [PaymentGenCodeItem::STATUS_UNPAID, PaymentGenCodeItem::STATUS_PAID]]);
                if (!empty($check)) {
                    continue;
                }
                $paymentGenCodeItem = new PaymentGenCodeItem();
                $paymentGenCodeItem->building_cluster_id = $paymentGenCode->building_cluster_id;
                $paymentGenCodeItem->payment_gen_code_id = $paymentGenCode->id;
                $paymentGenCodeItem->service_payment_fee_id = $servicePaymentFee->id;
                $paymentGenCodeItem->status = PaymentGenCodeItem::STATUS_UNPAID;
                $paymentGenCodeItem->amount = $servicePaymentFee->more_money_collecte;
                if (!$paymentGenCodeItem->save()) {
                    print_r($paymentGenCodeItem->errors);
                }
            }

            //add link thanh toán online vào email
            $WebPayment = Yii::$app->params['WebPayment'];
            $contentHtml .= '<br>Thông tin thanh toán online: <a target="_blank" href="' . $WebPayment['list_fee'] . '?code=' . $paymentGenCode->code . '">Thanh toán</a><br>';
        }

        if (!empty($announcementCampaign->attach)) {
            $attachs = json_decode($announcementCampaign->attach, true);
            if (!empty($attachs)) {
                if (!empty($attachs['fileImageList'])) {
                    foreach ($attachs['fileImageList'] as $item) {
                        $contentHtml .= '<img style="border-radius: 5px;padding: 5px;margin: 5px;max-width: 150px;max-height: 150px;" src="' . $domain_link . $item['url'] . '" >';
                    }
                }
                if (!empty($attachs['fileList'])) {
                    foreach ($attachs['fileList'] as $item) {
                        $contentHtml .= '<br><a target="_blank" style="color: #3c8dbc;" href="' . $domain_link . $item['url'] . '">' . $item['name'] . '</a>';
                    }
                }
            }
        }

        //chỉ gửi email cho chủ hộ
        $email_to = '';
        if (!empty($apartment->residentUser)) {
            if ($apartment->residentUser->is_send_email == ApartmentMapResidentUser::IS_SEND_EMAIL && !empty($apartment->residentUser->email)) {
                $email_to = $apartment->residentUser->email;
                $announcementItem->email = $apartment->residentUser->email;
            }
        }

        //gửi email ở đây
        $aws = Yii::$app->params['aws'];
        $subject = $title . ': ' . $announcementCampaign->title;
        $contentHtml .= '<img style="width: 0;height: 0;" src="' . $domain_link . '/read/email?code=' . $announcementItem->id . '">';
        if (!empty($aws) && !empty($email_to)) {
            $callback['url'] = $domain_link . '/callback/email';
            $payload = [
                'sender' => utf8_encode($buildingCluster->name) . ' <' . $aws['sender'] . '>',
                'aws_config' => $aws['config'],
                'subject' => $subject,
                'content' => $contentHtml,
                'callback' => $callback
            ];

            if (!empty($attachments) || !empty($content_pdf)) {
                if (!empty($attachments)) {
                    $payload['attachments'] = $attachments;
                }
                if (!empty($content_pdf)) {
                    $payload['content_pdf'] = $content_pdf;
                }
                $payload['to'] = $email_to;
                print_r($payload);
                QueueLib::channelEmailAwsAttachments(Json::encode($payload));
            } else {
                $payload['to'] = [$email_to];
                QueueLib::channelEmailAws(Json::encode($payload));
            }
            $is_send_email = 1;
        }
        //end gửi email ở đây
//        }

        if ($announcementCampaign->is_send_sms == AnnouncementCampaign::IS_SEND_SMS) {
            //gửi tin nhắn ở đây
            $contentSms = $announcementItem->content_sms;
            $phone_to = null;
            if (!empty($apartment->residentUser)) {
                $phone_to = $apartment->residentUser->phone;
                $announcementItem->phone = $apartment->residentUser->phone;
            }
            if (!empty($phone_to)) {
                $callback['url'] = $domain_link . '/callback/sms';
                $payload = [
                    'to' => $phone_to,
                    'utf' => true,
                    'content' => $contentSms,
                    'callback' => $callback
                ];
                $payload = array_merge($payload, $SmsCmc);
                QueueLib::channelSms(Json::encode($payload));
                $is_send_sms = 1;
            }
            //end gửi tin nhắn ở đây
        }

        if (!$announcementItem->save()) {
            print_r($announcementItem->getErrors());
        }

        //tăng số lần nhắc nợ của căn hộ lên
        if ($is_reminder_debt === 1) {
            //nếu có 1 trong các phương thức nhắc nợ thì mới tăng số lần nhắc nợ
            if ($is_send_email >= 1 || $is_send_app >= 1 || $is_send_sms >= 1) {
                if ($apartment->reminder_debt < 5) {
                    $apartment->reminder_debt++;
                    if (!$apartment->save()) {
                        print_r($apartment->getErrors());
                    }
                }

                $serviceDebt = ServiceDebt::findOne(['apartment_id' => $apartment->id, 'type' => ServiceDebt::TYPE_CURRENT_MONTH]);
                if (!empty($serviceDebt)) {
                    if ($serviceDebt->status < 5) {
                        $serviceDebt->status = $apartment->reminder_debt;
                        if (!$serviceDebt->save()) {
                            print_r($serviceDebt->getErrors());
                        }
                    }
                }
            }
        }
        //update thông báo chưa đọc cho user resident
        ResidentUserMapRead::updateOrCreate(['is_read' => ResidentUserMapRead::IS_UNREAD], ['building_cluster_id' => $apartment->building_cluster_id, 'type' => ResidentUserMapRead::TYPE_ANNOUNCEMENT, 'resident_user_id' => $residentUserIds], $residentUserIds);
        return [
            'is_send_sms' => $is_send_sms,
            'is_send_email' => $is_send_email,
            'is_send_app' => $is_send_app,
        ];
    }

    private function genContent($announcementCampaign, $apartment, $announcementItemSend)
    {
        $content_new = str_replace("{{APARTMENT_NAME}}", $apartment->name, $announcementCampaign->content);
        $content_new = str_replace("{{RESIDENT_NAME}}", $apartment->resident_user_name, $content_new);
        if(!empty($announcementItemSend)){
            $content_new = str_replace("{{TOTAL_FEE}}", CUtils::formatPrice($announcementItemSend->end_debt) . 'vnd', $content_new);
        }

        //check nếu có key table thì mới lấy dữ liệu bảng
        if (strpos($content_new, '{{TABLE_ALL_FEE}}') !== false) {
            $table_all_fee = $this->render('/pdf/all_service_fee', ['apartment' => $apartment]);
            $content_new = str_replace("{{TABLE_ALL_FEE}}", $table_all_fee, $content_new);
        }
        return $content_new;
    }

    private function getContentSms($announcementCampaign, $apartment, $announcementItemSend)
    {
        $content_sms_new = str_replace("{{APARTMENT_NAME}}", $apartment->name, $announcementCampaign->content_sms);
        $content_sms_new = str_replace("{{RESIDENT_NAME}}", $apartment->resident_user_name, $content_sms_new);
        if(!empty($announcementItemSend)){
            $content_sms_new = str_replace("{{TOTAL_FEE}}", CUtils::formatPrice($announcementItemSend->end_debt) . 'vnd', $content_sms_new);
        }
        return $content_sms_new;
    }

    public function sendToApartmentNew($announcementCampaign, $announcementCategory, $buildingCluster, $announcementItemSend, $apartment,&$isCheckSendEmail = null,&$count = null)
    {
        //lấy ra các config cần thiết
        $link_api = Yii::$app->params['link_api'];
        $domain_link = $link_api['web'];
        $HeaderKey = Yii::$app->params['HeaderKey'];

        //khai báo các giá trị cần check
        $attachments = [];
        $content_pdf = [];
        $is_send_app = 0;
        $is_send_email = 0;
        $is_send_sms = 0;
        $is_reminder_debt = 0;
        if (
        in_array(
            $announcementCampaign->type,
            [
                AnnouncementCampaign::TYPE_REMINDER_DEBT_1,
                AnnouncementCampaign::TYPE_REMINDER_DEBT_2,
                AnnouncementCampaign::TYPE_REMINDER_DEBT_3,
                AnnouncementCampaign::TYPE_REMINDER_DEBT_4,
                AnnouncementCampaign::TYPE_REMINDER_DEBT_5
            ]
        )
        ) {
            $is_reminder_debt = 1;
//            $name_file = 'payment_fee_' . date('dmY', time()) . '_' . $apartment->id . '.pdf';
//            $content_pdf = [
//                'name' => $name_file,
//                'data' => $domain_link . '/read/pdf?apartment_id=' . $apartment->id . '&campaign_type=' . $announcementCampaign->type
//            ];
        }
        // Tạo danh sách người nhận tin tức
        $announcementItem = self::createAnnouncementItem($announcementCampaign, $announcementItemSend, $apartment);

        if (empty($announcementItem)) {
            echo "createAnnouncementItem Empty\n";
            return false;
        }

        $callback = [
            'id' => $announcementItem->id,
            'url' => '',
            'api_key_name' => $HeaderKey['HEADER_API_KEY'],
            'api_key_value' => $HeaderKey['API_KEY_WEB'],
        ];

        $player_ids = [];
        $title = $content = NotificationTemplate::vsprintf(NotificationTemplate::NOTIFICATION_NEW_TO_RESIDENT, [$announcementCampaign->title]);
        $title_en = $content_en = NotificationTemplate::vsprintf(NotificationTemplate::NOTIFICATION_NEW_TO_RESIDENT_EN, [$announcementCampaign->title_en]);
        $residentUserIds = []; // user cần update trạng thái thông báo là chưa đọc
        //tạo bảng log notify hiển thị trên app
        $resUserNotify = self::createResidentUserNotify($announcementCampaign, $apartment, $announcementItem, $title, $content, $title_en, $content_en, $residentUserIds, $player_ids, $is_send_app);
        if(!empty($resUserNotify)){
            $player_ids = $resUserNotify['player_ids'];
            $is_send_app = $resUserNotify['is_send_app'];
            $residentUserIds = $resUserNotify['residentUserIds'];
        }

        //gửi thông báo tới app
        self::pushNotificationApp($announcementCampaign, $announcementItem, $player_ids, $domain_link, $title, $content, $title_en, $content_en, $callback);

        if (!empty($player_ids)) {
            $announcementItem->device_token = implode(',', $player_ids);
        }

        //lấy ra danh sách gửi email và nội dung gửi email
        $is_send_email = self::sendEmail($buildingCluster, $apartment, $announcementCampaign, $announcementItem, $is_reminder_debt, $title, $domain_link, $is_send_email, $attachments, $content_pdf, $callback,$announcementItemSend, $isCheckSendEmail,$count);


        //gửi sms
        $is_send_sms = self::sendSms($buildingCluster, $apartment, $announcementCampaign, $announcementItem, $is_send_sms, $domain_link, $callback);

        //nếu là thông báo khảo sát, thì tạo dữ liệu khảo sát
        if($announcementCampaign->is_survey == AnnouncementCampaign::IS_SURVEY){
            self::createResidentUserSurvey($apartment, $announcementCampaign);
        }
        //end tạo survey

        if (!$announcementItem->save()) {
            print_r($announcementItem->getErrors());
        }

        self::countIndexDebit($apartment, $is_reminder_debt, $is_send_email, $is_send_app, $is_send_sms);

        //update thông báo chưa đọc cho user resident
        ResidentUserMapRead::updateOrCreate(['is_read' => ResidentUserMapRead::IS_UNREAD], ['building_cluster_id' => $apartment->building_cluster_id, 'type' => ResidentUserMapRead::TYPE_ANNOUNCEMENT, 'resident_user_id' => $residentUserIds], $residentUserIds);
        return [
            'is_send_sms' => $is_send_sms,
            'is_send_email' => $is_send_email,
            'is_send_app' => $is_send_app,
        ];
    }

    public function sendToAppendEmailAndPhone($announcementCampaign, $announcementCategory, $announcementItemSend, $buildingCluster)
    {
        echo "sendToAppendEmailAndPhone\n";
        //lấy ra các config cần thiết
        $link_api = Yii::$app->params['link_api'];
        $domain_link = $link_api['web'];
        $HeaderKey = Yii::$app->params['HeaderKey'];

        //khai báo các giá trị cần check
        $attachments = [];
        $is_send_app = 0;
        $is_send_email = 0;
        $is_send_sms = 0;


        $callback = [
            'id' => null,
            'url' => '',
            'api_key_name' => $HeaderKey['HEADER_API_KEY'],
            'api_key_value' => $HeaderKey['API_KEY_WEB'],
        ];

        $title = NotificationTemplate::vsprintf(NotificationTemplate::NOTIFICATION_NEW_TO_RESIDENT, [$announcementCampaign->title]);

        //gửi email
        if(!empty($announcementCampaign->add_email_send)){
            $arrEmails = Json::decode($announcementCampaign->add_email_send, true);
            foreach ($arrEmails as $send_email){
                $announcementItem = self::createAnnouncementItem($announcementCampaign, $announcementItemSend);
                if (empty($announcementItem)) {
                    echo "createAnnouncementItem Empty\n";
                    return false;
                }
                $announcementItem->email = $send_email;
                if (!$announcementItem->save()) {
                    print_r($announcementItem->getErrors());
                }
                $callback['id'] = $announcementItem->id;
                self::sendEmailByAdd($buildingCluster, $announcementCampaign, $announcementItem, $title, $domain_link, $send_email, $attachments, $callback);
            }
        }


        //gửi sms
        if(!empty($announcementCampaign->add_phone_send)){
            $arrPhones = Json::decode($announcementCampaign->add_phone_send, true);
            foreach ($arrPhones as $send_phone){
                $announcementItem = self::createAnnouncementItem($announcementCampaign, $announcementItemSend);
                if (empty($announcementItem)) {
                    echo "createAnnouncementItem Empty\n";
                    return false;
                }
                $announcementItem->phone = $send_phone;
                if (!$announcementItem->save()) {
                    print_r($announcementItem->getErrors());
                }
                $callback['id'] = $announcementItem->id;
                self::sendSmsByAdd($buildingCluster, $announcementCampaign, $announcementItem, $send_phone, $domain_link, $callback);
            }
        }



        return [
            'is_send_sms' => $is_send_sms,
            'is_send_email' => $is_send_email,
            'is_send_app' => $is_send_app,
        ];
    }

    /*
     * return AnnouncementItem
     */
    private function createAnnouncementItem($announcementCampaign, $announcementItemSend, $apartment = null)
    {
        echo "createAnnouncementItem\n";
        $announcementItem = null;
        if(!empty($announcementCampaign->targets) && !empty($apartment))
        {
            $targets = Json::decode($announcementCampaign->targets);
            $qArray = [
                'apartment_id' => $apartment->id,
                'type' => $targets,
                'is_deleted' => ApartmentMapResidentUser::NOT_DELETED
            ];
            $resident_user_phones = Json::decode($announcementCampaign->resident_user_phones);
            if(!empty($resident_user_phones)){
                $apartmentMapResidentUsers = ApartmentMapResidentUser::find()
                    ->where($qArray)
                    ->andWhere(['NOT', ['resident_user_phone' => $resident_user_phones]])
                    ->all();
            }else{
                $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where($qArray)->all();
            }
            foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser){
                $aryQuery = [
                    'building_cluster_id'=> $announcementCampaign->building_cluster_id,
                    'announcement_campaign_id'=> $announcementCampaign->id,
                    'apartment_id'=> $apartment->id,
                    'phone'=> $apartmentMapResidentUser->resident_user_phone,
                ];
                if(!empty($announcementItemSend))
                {
                    $aryQuery['building_area_id'] = $announcementItemSend->building_area_id;
                }

                $checkItem = AnnouncementItem::find()->where($aryQuery)->one();
                if(!empty($checkItem))
                {
                    continue;
                }
                $announcementItem = new AnnouncementItem();
                $announcementItem->announcement_campaign_id = $announcementCampaign->id;
                $announcementItem->building_cluster_id = $announcementCampaign->building_cluster_id;
                if(!empty($announcementItemSend)){
                    $announcementItem->building_area_id = $announcementItemSend->building_area_id;
                    $announcementItem->announcement_item_send_id = $announcementItemSend->id;
                    $announcementItem->end_debt = $announcementItemSend->end_debt;
                    $announcementItem->type = $announcementItemSend->type;
                }
                $announcementItem->description = $announcementCampaign->description;
                $announcementItem->content = $announcementCampaign->content;
                $announcementItem->content_sms = $announcementCampaign->content_sms;
                $announcementItem->status_notify = AnnouncementItem::STATUS_SUCCESS; // mặc định là thành công
                $announcementItem->apartment_id = $apartment->id;
                $announcementItem->resident_user_name = $apartmentMapResidentUser->resident_user_first_name . ' ' . $apartmentMapResidentUser->resident_user_last_name;
                $announcementItem->phone = $apartmentMapResidentUser->resident_user_phone;
                $announcementItem->email = $apartmentMapResidentUser->resident_user_email;
                $announcementItem->content = self::genContent($announcementCampaign, $apartment, $announcementItemSend);
                $announcementItem->description = CVietnameseTools::truncateString(strip_tags($announcementItem->content), 100);
                $announcementItem->content_sms = self::getContentSms($announcementCampaign, $apartment, $announcementItemSend);
                if (!$announcementItem->save()) {
                    print_r($announcementItem->getErrors());
                    return false;
                }
            }
        }else
        {
            $announcementItem = new AnnouncementItem();
            $announcementItem->announcement_campaign_id = $announcementCampaign->id;
            $announcementItem->building_cluster_id = $announcementCampaign->building_cluster_id;
            if(!empty($announcementItemSend)){
                $announcementItem->building_area_id = $announcementItemSend->building_area_id;
                $announcementItem->announcement_item_send_id = $announcementItemSend->id;
                $announcementItem->end_debt = $announcementItemSend->end_debt;
                $announcementItem->type = $announcementItemSend->type;
            }
            $announcementItem->description = $announcementCampaign->description;
            $announcementItem->content = $announcementCampaign->content;
            $announcementItem->content_sms = $announcementCampaign->content_sms;
            $announcementItem->status_notify = AnnouncementItem::STATUS_SUCCESS; // mặc định là thành công
            if (!$announcementItem->save()) {
                print_r($announcementItem->getErrors());
                return false;
            }
        }
        return $announcementItem;
    }

    /*
     * tao log ResidentUserNotify
     */
    private function createResidentUserNotify($announcementCampaign, $apartment, $announcementItem, $title, $content, $title_en, $content_en, $residentUserIds, $player_ids, $is_send_app)
    {
        echo "createResidentUserNotify\n";
        //lấy id nhận thông báo được config
        $residentUserIdSends = [];
        if(!empty($apartment->apartmentMapResidentUserReceiveNotifyFees)){
            foreach ($apartment->apartmentMapResidentUserReceiveNotifyFees as $apartmentMapResidentUserReceiveNotifyFee){
                $residentUserIdSends[] = $apartmentMapResidentUserReceiveNotifyFee->resident_user_id;
            }
        }
        $qArray = ['apartment_id' => $apartment->id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED];
        if(!empty($announcementCampaign->targets)){
            $targets = Json::decode($announcementCampaign->targets);
            $qArray['type'] = $targets;
        }
        $resident_user_phones = Json::decode($announcementCampaign->resident_user_phones);
        if(!empty($resident_user_phones)){
            $apartmentMapResidentUsers = ApartmentMapResidentUser::find()
                ->where($qArray)
                ->andWhere(['NOT', ['resident_user_phone' => $resident_user_phones]])
                ->all();
        }else{
            $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where($qArray)->all();
        }
        // gửi thông báo cho resident
        $aryIdapartmentMapResidentUsers = [];
        foreach($apartmentMapResidentUsers as $apartmentMapResidentUser )
        {
            if(empty($apartmentMapResidentUser->resident->id))
            {
                continue;
            }
            $aryIdapartmentMapResidentUsers[] = $apartmentMapResidentUser->resident->id;
        }
        $aryIdapartmentMapResidentUsers = array_unique($aryIdapartmentMapResidentUsers);
        
        foreach($aryIdapartmentMapResidentUsers as $aryIdapartmentMapResidentUser)
        {
            //khởi tạo log cho từng resident user
            $residentUserNotify = new ResidentUserNotify();
            $residentUserNotify->building_cluster_id = $announcementItem->building_cluster_id;
            $residentUserNotify->building_area_id = $announcementItem->building_area_id;
            $residentUserNotify->resident_user_id = $aryIdapartmentMapResidentUser ?? null;
            $residentUserNotify->type = ResidentUserNotify::TYPE_ANNOUNCEMENT;
            $residentUserNotify->announcement_item_id = $announcementItem->id;
            $residentUserNotify->title = $title;
            $residentUserNotify->description = $content;
            $residentUserNotify->title_en = $title_en;
            $residentUserNotify->description_en = $content_en;
            if (!$residentUserNotify->save()) {
                print_r($residentUserNotify->getErrors());
            }
            //end log
        }
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser) {

            //loại bỏ thông tin ko cần gửi
//            if(!empty($residentUserIdSends)){
//                if(!in_array($apartmentMapResidentUser->resident_user_id, $residentUserIdSends)){
//                    continue;
//                }
//            }

            if($apartmentMapResidentUser->resident_user_is_send_notify != ResidentUser::IS_SEND_NOTIFY){
                continue;
            }

            $device_token = [];
            $residentUserIds = [];
            echo "apartmentMapResidentUser id: " . $apartmentMapResidentUser->id;
            //lấy thông tin device token cần gửi
            if(!empty($apartmentMapResidentUser->resident)){
                echo "resident_user_id: " . $apartmentMapResidentUser->resident->id ?? null;
                $residentUserIds[] = $apartmentMapResidentUser->resident->id;
                $residentUserDeviceTokens = ResidentUserDeviceToken::find()->where(['resident_user_id' => $apartmentMapResidentUser->resident->id])->all();
//            if (!empty($apartmentMapResidentUser->residentUserDeviceTokens)) {
//                $residentUserDeviceTokens = $apartmentMapResidentUser->residentUserDeviceTokens;
                foreach ($residentUserDeviceTokens as $residentUserDeviceToken) {
                    $device_token[] = $residentUserDeviceToken->device_token;
                    if($residentUserDeviceToken->resident_user_id == 164 || $residentUserDeviceToken->resident_user_id == 176)
                    {
                        continue;
                    }
                    $player_ids[] = $residentUserDeviceToken->device_token;
                }
//            }
            }
                var_dump($device_token);
                var_dump($player_ids);
//            if (!empty($device_token) && (!empty($residentUserIdSends) || $apartmentMapResidentUser->type == ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD)) {
                $is_send_app = 1;
                echo "is_send_app: $is_send_app\n";
//            }
        }
        return [
            'player_ids' => $player_ids,
            'is_send_app' => $is_send_app,
            'residentUserIds' => $residentUserIds,
        ];
    }

    /*
     * tao log ResidentUserSurvey
     */
    private function createResidentUserSurvey($apartment, $announcementCampaign)
    {
        echo "createResidentUserSurvey\n";
        if(!empty($announcementCampaign->targets)){
            $targets = Json::decode($announcementCampaign->targets);
//            $typeArr = [];
//            foreach ($targets as $target){
//                if ($target == AnnouncementCampaign::TARGET_CH){ $typeArr[] = ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD;}
//                if ($target == AnnouncementCampaign::TARGET_TV){ $typeArr[] = ApartmentMapResidentUser::TYPE_MEMBER;}
//                if ($target == AnnouncementCampaign::TARGET_KH){ $typeArr[] = ApartmentMapResidentUser::TYPE_GUEST;}
//            }
            $qArray = ['apartment_id' => $apartment->id, 'type' => $targets, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED];
            $resident_user_phones = Json::decode($announcementCampaign->resident_user_phones);
            if(!empty($resident_user_phones)){
                $apartmentMapResidentUsers = ApartmentMapResidentUser::find()
                    ->where($qArray)
                    ->andWhere(['NOT', ['resident_user_phone' => $resident_user_phones]])
                    ->all();
            }else{
                $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where($qArray)->all();
            }

            foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser){
                //khởi tạo log cho từng resident user
                $announcementSurvey = new AnnouncementSurvey();
                $announcementSurvey->building_cluster_id = $apartment->building_cluster_id;
                $announcementSurvey->building_area_id = $apartment->building_area_id;
                $announcementSurvey->apartment_id = $apartment->id;
                $announcementSurvey->resident_user_id = $apartmentMapResidentUser->resident->id ?? null;
                $announcementSurvey->apartment_capacity = $apartment->capacity;
                $announcementSurvey->announcement_campaign_id = $announcementCampaign->id;
                $announcementSurvey->status = AnnouncementSurvey::STATUS_DEFAULT;
                if (!$announcementSurvey->save()) {
                    print_r($announcementSurvey->getErrors());
                }
                //end log
            }
        }
        return true;
    }

    /*
     * push notification app
     */
    private function pushNotificationApp($announcementCampaign, $announcementItem, $player_ids, $domain_link, $title, $content, $title_en, $content_en, $callback)
    {
        echo "pushNotificationApp\n";
        if ($announcementCampaign->is_send_push == AnnouncementCampaign::IS_SEND_PUSH) {
            echo "is push\n";
            //gửi thông báo theo device token
            $data = [
                'type' => 'notify',
                'notify_id' => $announcementItem->id,
            ];
            $callback['url'] = $domain_link . '/callback/notify';
            $oneSignalApi = new OneSignalApi();
            $oneSignalApi->sendToWorkerPlayerIds($title, $content, $title_en, $content_en, $player_ids, $data, null, null, $callback);
            //end gửi thông báo theo device token
        }
    }

    /*
     * send email
     */
    private function sendEmail($buildingCluster, $apartment, $announcementCampaign, &$announcementItem, $is_reminder_debt, $title, $domain_link, $is_send_email, $attachments, $content_pdf, $callback = [],$announcementItemSend = null,&$isCheckSendEmail,&$count)
    {
        echo "sendEmail\n";
        if ($announcementCampaign->is_send_email == AnnouncementCampaign::IS_SEND_EMAIL) {
            echo "is send email\n";
            $contentHtml = $announcementItem->content;
            //nếu là email nhắc nợ thì check gen code để add link thanh toán online vào email luôn
            if ($is_reminder_debt == 1 && $announcementCampaign->type > AnnouncementCampaign::TYPE_REMINDER_DEBT_1) {
                $paymentGenCode = PaymentGenCode::findOne(['apartment_id' => $apartment->id, 'is_auto' => PaymentGenCode::IS_AUTO, 'status' => PaymentGenCode::STATUS_UNPAID]);
                if (empty($paymentGenCode)) {
                    $paymentGenCode = new PaymentGenCode();
                    $paymentGenCode->building_cluster_id = $apartment->building_cluster_id;
                    $paymentGenCode->apartment_id = $apartment->id;
                    $paymentGenCode->is_auto = PaymentGenCode::IS_AUTO;
                    $paymentGenCode->status = PaymentGenCode::STATUS_UNPAID;
                    $paymentGenCode->type = PaymentGenCode::PAY_ONLINE;
                    $paymentGenCode->generateCode();
                    if (!$paymentGenCode->save()) {
                        print_r($paymentGenCode->errors);
                    }
                }
                //lấy các item mới nhất
                PaymentGenCodeItem::deleteAll(['payment_gen_code_id' => $paymentGenCode->id, 'status' => PaymentGenCodeItem::STATUS_UNPAID]);
                //check phi nằm trong yêu cầu thanh toán khác
                $paymentGenCodes = PaymentGenCode::find()->where(['apartment_id' => $apartment->id, 'status' => PaymentGenCode::STATUS_UNPAID])->all();
                $id_fee_not_ins = [];
                foreach ($paymentGenCodes as $paymentGenCode){
                    $paymentGenCodeItems = PaymentGenCodeItem::find()->where(['payment_gen_code_id' => $paymentGenCode->id, 'status' => PaymentGenCodeItem::STATUS_UNPAID])->all();
                    foreach ($paymentGenCodeItems as $paymentGenCodeItem){
                        $id_fee_not_ins[] = $paymentGenCodeItem->service_payment_fee_id;
                    }
                }

                $servicePaymentFees = ServicePaymentFee::find()->where(['building_cluster_id' => $paymentGenCode->building_cluster_id, 'apartment_id' => $paymentGenCode->apartment_id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT, 'is_debt' => ServicePaymentFee::IS_DEBT])
                    ->andWhere(['>', 'more_money_collecte', 0])
                    ->andWhere(['not', ['id' => $id_fee_not_ins]])->all();
                if(!empty($servicePaymentFees)){
                    foreach ($servicePaymentFees as $servicePaymentFee) {
                        $paymentGenCodeItem = PaymentGenCodeItem::findOne(['payment_gen_code_id' => $paymentGenCode->id, 'service_payment_fee_id' => $servicePaymentFee->id, 'status' => [PaymentGenCodeItem::STATUS_UNPAID, PaymentGenCodeItem::STATUS_PAID]]);
                        if (empty($paymentGenCodeItem)) {
                            $paymentGenCodeItem = new PaymentGenCodeItem();
                            $paymentGenCodeItem->building_cluster_id = $paymentGenCode->building_cluster_id;
                            $paymentGenCodeItem->payment_gen_code_id = $paymentGenCode->id;
                            $paymentGenCodeItem->service_payment_fee_id = $servicePaymentFee->id;
                        }
                        $paymentGenCodeItem->status = PaymentGenCodeItem::STATUS_UNPAID;
                        $paymentGenCodeItem->amount = $servicePaymentFee->more_money_collecte;
                        if (!$paymentGenCodeItem->save()) {
                            print_r($paymentGenCodeItem->errors);
                        }
                    }
                    $code_gen = $paymentGenCode->code;
                }else{
                    if(!$paymentGenCode->delete()){
                        Yii::warning('Xóa yêu cầu ko có phí');
                    }
                    $code_gen = '(Không còn phí)';
                }
                $contentHtml = str_replace("{{PAYMENT_CODE}}", $code_gen, $contentHtml);
                $announcementItem->content = str_replace("{{PAYMENT_CODE}}", $code_gen, $announcementItem->content);

                //add link thanh toán online vào email
//                $WebPayment = Yii::$app->params['WebPayment'];
//                $contentHtml .= '<br>Thông tin thanh toán online: <a target="_blank" href="' . $WebPayment['list_fee'] . '?code=' . $paymentGenCode->code . '">Thanh toán</a><br>';
            }
            if($is_reminder_debt == 1){
                //tao file phi
                $arr_file_fee = self::exportPaymentFee($apartment, '');
                if(!empty($arr_file_fee)){
                    $contentHtml .= '<br><a target="_blank" style="color: #3c8dbc;" href="' . $domain_link . $arr_file_fee['url'] . '">' . $arr_file_fee['name'] . '</a>';
                }
            }
            if (!empty($announcementCampaign->attach)) {
                $attachs = Json::decode($announcementCampaign->attach, true);
                if (!empty($attachs)) {
                    if (!empty($attachs['fileImageList'])) {
                        foreach ($attachs['fileImageList'] as $item) {
                            $contentHtml .= '<img style="border-radius: 5px;padding: 5px;margin: 5px;max-width: 150px;max-height: 150px;" src="' . $domain_link . $item['url'] . '" >';
                        }
                    }
                    if (!empty($attachs['fileList'])) {
                        foreach ($attachs['fileList'] as $item) {
                            $contentHtml .= '<br><a target="_blank" style="color: #3c8dbc;" href="' . $domain_link . $item['url'] . '">' . $item['name'] . '</a>';
                        }
                    }
                }
            }

            //lấy email được config
            $emailTos = self::getEmailSend($apartment,$announcementCampaign,$announcementItem,$announcementItemSend);

            //gửi email ở đây
            $subject = $title . ': ' . $announcementCampaign->title;
            $contentHtml .= '<img style="width: 0;height: 0;" src="' . $domain_link . '/read/email?code=' . $announcementItem->id . '">';
            // Thiết lập thông tin email
            // $emailTos = ['hoang682681@gmail.com','hoanganh180798@gmail.com'];
            // print_r($emailTos);
            foreach ($emailTos as $email_to){
                $isCheckSendEmail[$count]['email'][] = $email_to;    
                $isCheckSendEmail[$count]['subject'] = $subject;      
                $isCheckSendEmail[$count]['contentHtml'] = $contentHtml;            
            }
            $count++;                         

            // Gửi email
            // $aws = Yii::$app->params['aws'];
            // if (!empty($aws) && !empty($emailTos)) {
            //     $announcementItem->email = implode(',', $emailTos);

            //     $callback['url'] = $domain_link . '/callback/email';
            //     $payload = [
            //         'sender' => utf8_encode($buildingCluster->name) . ' <' . $aws['sender'] . '>',
            //         'aws_config' => $aws['config'],
            //         'subject' => $subject,
            //         'content' => $contentHtml,
            //         'callback' => $callback
            //     ];

            //     if (!empty($attachments) || !empty($content_pdf)) {
            //         if (!empty($attachments)) {
            //             $payload['attachments'] = $attachments;
            //         }
            //         if (!empty($content_pdf)) {
            //             $payload['content_pdf'] = $content_pdf;
            //         }
            //         foreach ($emailTos as $email_to){
            //             $payload['to'] = $email_to;
            //             QueueLib::channelEmailAwsAttachments(Json::encode($payload));
            //         }
            //     } else {
            //         $payload['to'] = $emailTos;
            //         QueueLib::channelEmailAws(Json::encode($payload));
            //     }
            //     $is_send_email = 1;
            // }
            //end gửi email ở đây
        }
        return $is_send_email;
    }

    private function sendEmailByAdd($buildingCluster, $announcementCampaign, $announcementItem, $title, $domain_link, $send_email, $attachments, $callback = [])
    {
        if ($announcementCampaign->is_send_email == AnnouncementCampaign::IS_SEND_EMAIL && !empty($send_email)) {
            $contentHtml = $announcementItem->content;

            if (!empty($announcementCampaign->attach)) {
                $attachs = Json::decode($announcementCampaign->attach, true);
                if (!empty($attachs)) {
                    if (!empty($attachs['fileImageList'])) {
                        foreach ($attachs['fileImageList'] as $item) {
                            $contentHtml .= '<img style="border-radius: 5px;padding: 5px;margin: 5px;max-width: 150px;max-height: 150px;" src="' . $domain_link . $item['url'] . '" >';
                        }
                    }
                    if (!empty($attachs['fileList'])) {
                        foreach ($attachs['fileList'] as $item) {
                            $contentHtml .= '<br><a target="_blank" style="color: #3c8dbc;" href="' . $domain_link . $item['url'] . '">' . $item['name'] . '</a>';
                        }
                    }
                }
            }

            //lấy email được config
            $emailTos = [$send_email];

            //gửi email ở đây
            $aws = Yii::$app->params['aws'];
            $subject = $title . ': ' . $announcementCampaign->title;
            $contentHtml .= '<img style="width: 0;height: 0;" src="' . $domain_link . '/read/email?code=' . $announcementItem->id . '">';
            if (!empty($aws) && !empty($emailTos)) {
                $callback['url'] = $domain_link . '/callback/email';
                $payload = [
                    'sender' => utf8_encode($buildingCluster->name) . ' <' . $aws['sender'] . '>',
                    'aws_config' => $aws['config'],
                    'subject' => $subject,
                    'content' => $contentHtml,
                    'callback' => $callback
                ];

                if (!empty($attachments) || !empty($content_pdf)) {
                    if (!empty($attachments)) {
                        $payload['attachments'] = $attachments;
                    }
                    if (!empty($content_pdf)) {
                        $payload['content_pdf'] = $content_pdf;
                    }
                    foreach ($emailTos as $email_to){
                        $payload['to'] = $email_to;
                        QueueLib::channelEmailAwsAttachments(Json::encode($payload));
                    }
                } else {
                    $payload['to'] = $emailTos;
                    QueueLib::channelEmailAws(Json::encode($payload));
                }
            }
            //end gửi email ở đây
        }
    }

    /*
     * return array email
     */
    private function getEmailSend($apartment,$announcementCampaign = null,$announcementItem = null,$announcementItemSend = null){
        //lấy email được config
        $emailTos = [];
        // if(!empty($apartment->apartmentMapResidentUserReceiveNotifyFees)){
        //     foreach ($apartment->apartmentMapResidentUserReceiveNotifyFees as $apartmentMapResidentUserReceiveNotifyFee){
        //         $emailTos[] = $apartmentMapResidentUserReceiveNotifyFee->email;
        //     }
        // }
        // //chỉ gửi email cho chủ hộ
        // if (empty($emailTos) && !empty($apartment->residentUser)) {
        //     if ($apartment->residentUser->is_send_email == ApartmentMapResidentUser::IS_SEND_EMAIL && !empty($apartment->residentUser->email)) {
        //         $emailTos[] = $apartment->residentUser->email;
        //     }
        // }
        $AnnouncementItemSearch = new AnnouncementItemSearch();
        $aryIdApartment = [];
        // $aryIdApartment = $announcementCampaign->apartment_ids;
        // if(1 == $announcementCampaign->type)
        // {
        //     $aryIdApartment = $announcementCampaign->apartment_send_ids;
        // }
        $apartments = Apartment::find()->where(['building_area_id' => $announcementItemSend->building_area_id])->all();
        if(!empty($apartments))
        {
            foreach($apartments as $apartment )
            {
                $aryIdApartment[] = $apartment->id;
            }
        }
        $data = $AnnouncementItemSearch->searchByIdAparmentMapResidentUser($aryIdApartment,$announcementCampaign->targets);
        $dataResults = [];
        $residentUserPhones = $announcementCampaign->resident_user_phones ?? [];
        $aryEmailList = [];
        foreach($data as $itemValue)
        {
            $dataResults[$itemValue->resident_user_phone] = $itemValue->resident_user_email;
        }
        foreach($dataResults as $key => $value)
        {
            if(!empty($residentUserPhones))
            {
                foreach($residentUserPhones as $residentUserPhone)
                {
                    if($key == $residentUserPhone)
                    {
                        continue;
                    }
                    $aryEmailList[] = $value;
                }
            }
        }
        if(empty($residentUserPhones))
        {
            foreach($dataResults as $key => $value)
            {
                if($value == null)
                {
                    continue;
                }
                $aryEmailList[] = $value;
            }
        }
        // lấy thêm email ở bảng item 
        if (!in_array($announcementCampaign->type, [AnnouncementCampaign::TYPE_REMINDER_DEBT_1, AnnouncementCampaign::TYPE_REMINDER_DEBT_2, AnnouncementCampaign::TYPE_REMINDER_DEBT_3, AnnouncementCampaign::TYPE_REMINDER_DEBT_4, AnnouncementCampaign::TYPE_REMINDER_DEBT_5])){
            $announcementItems = AnnouncementItem::find()->where(['announcement_campaign_id'=>$announcementCampaign->id])->all();
            if(!empty($announcementItems))
            {
                foreach($announcementItems as $announcementItem)
                {
                    if(empty($announcementItem->email))
                    {
                        continue;
                    }
                    $aryEmailList[] = $announcementItem->email;
                }
            }
        }
        $aryEmailList = array_unique($aryEmailList);
        return $aryEmailList;
    }
    
    /*
     * send sms
     */
    private function sendSms($buildingCluster, $apartment, $announcementCampaign, &$announcementItem, $is_send_sms, $domain_link, $callback = []){
        if ($announcementCampaign->is_send_sms == AnnouncementCampaign::IS_SEND_SMS) {
            $SmsCmc = Yii::$app->params['SmsCmc'];
            //check thong tin building cluster có tài khoản gửi sms thì dùng
            if (!empty($buildingCluster->sms_account_push) && !empty($buildingCluster->sms_password_push) && !empty($buildingCluster->sms_brandname_push)) {
                $SmsCmc = [
                    'Brandname' => $buildingCluster->sms_brandname_push,
                    'user' => $buildingCluster->sms_account_push,
                    'pass' => $buildingCluster->sms_password_push,
                ];
            }

            //gửi tin nhắn ở đây
            $contentSms = $announcementItem->content_sms;

            //lấy phone được config
            $phoneTos = self::getPhoneSend($apartment);
            if (!empty($phoneTos)) {
                $announcementItem->phone = implode(',', $phoneTos);

                $callback['url'] = $domain_link . '/callback/sms';
                $payload = [
                    'utf' => true,
                    'content' => $contentSms,
                    'callback' => $callback
                ];

                foreach ($phoneTos as $phoneTo){
                    $payload['to'] = $phoneTo;
                    $payload = array_merge($payload, $SmsCmc);
                    QueueLib::channelSms(Json::encode($payload));
                }
                $is_send_sms = 1;
            }
            //end gửi tin nhắn ở đây
        }
        return $is_send_sms;
    }

    private function sendSmsByAdd($buildingCluster, $announcementCampaign, $announcementItem, $send_phone, $domain_link, $callback = []){
        if ($announcementCampaign->is_send_sms == AnnouncementCampaign::IS_SEND_SMS && !empty($send_phone)) {
            $SmsCmc = Yii::$app->params['SmsCmc'];
            //check thong tin building cluster có tài khoản gửi sms thì dùng
            if (!empty($buildingCluster->sms_account_push) && !empty($buildingCluster->sms_password_push) && !empty($buildingCluster->sms_brandname_push)) {
                $SmsCmc = [
                    'Brandname' => $buildingCluster->sms_brandname_push,
                    'user' => $buildingCluster->sms_account_push,
                    'pass' => $buildingCluster->sms_password_push,
                ];
            }

            //gửi tin nhắn ở đây
            $contentSms = $announcementItem->content_sms;

            //lấy phone được config
            $phoneTos = [$send_phone];

            if (!empty($phoneTos)) {

                $callback['url'] = $domain_link . '/callback/sms';
                $payload = [
                    'utf' => true,
                    'content' => $contentSms,
                    'callback' => $callback
                ];

                foreach ($phoneTos as $phoneTo){
                    $payload['to'] = $phoneTo;
                    $payload = array_merge($payload, $SmsCmc);
                    QueueLib::channelSms(Json::encode($payload));
                }
            }
            //end gửi tin nhắn ở đây
        }
    }

    /*
     * return array phone
     */
    private function getPhoneSend($apartment){
        //lấy phone được config
        $phoneTos = [];
        if(!empty($apartment->apartmentMapResidentUserReceiveNotifyFees)){
            foreach ($apartment->apartmentMapResidentUserReceiveNotifyFees as $apartmentMapResidentUserReceiveNotifyFee){
                $phoneTos[] = $apartmentMapResidentUserReceiveNotifyFee->phone;
            }
        }

        //lấy số phone chủ hộ
        if (empty($phoneTos) && !empty($apartment->residentUser)) {
            $phoneTos[] = $apartment->residentUser->phone;
        }
        return $phoneTos;
    }

    /*
     * tăng số lần nhắc nợ
     */
    private function countIndexDebit($apartment, $is_reminder_debt, $is_send_email, $is_send_app, $is_send_sms){
        //tăng số lần nhắc nợ của căn hộ lên
        if ($is_reminder_debt == 1) {
            //nếu có 1 trong các phương thức nhắc nợ thì mới tăng số lần nhắc nợ
            if ($is_send_email >= 1 || $is_send_app >= 1 || $is_send_sms >= 1) {
                if ($apartment->reminder_debt < 5) {
                    $apartment->reminder_debt++;
                    if (!$apartment->save()) {
                        print_r($apartment->getErrors());
                    }
                }

                $serviceDebt = ServiceDebt::findOne(['apartment_id' => $apartment->id, 'type' => ServiceDebt::TYPE_CURRENT_MONTH]);
                if (!empty($serviceDebt)) {
                    if ($serviceDebt->status < 5) {
                        $serviceDebt->status = $apartment->reminder_debt;
                        if (!$serviceDebt->save()) {
                            print_r($serviceDebt->getErrors());
                        }
                    }
                }
            }
        }
    }

    private function exportPaymentFee($apartment, $name_file)
    {
        if(empty($apartment)){
            return null;
        }
        $lo = '';
        $tang = '';
        if(!empty($apartment->parent_path)){
            list($lo, $tang) = explode('/', trim($apartment->parent_path,'/'));
        }
        if(empty($name_file)){
            $name_file = 'Căn hộ: ' . $apartment->name .', Lô: '.$lo.', Tầng: '.$tang;
        }
        $dataProviderModels = ServicePaymentFeeResponse::find()
            ->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID])->orderBy(['service_map_management_id' =>  SORT_ASC, 'fee_of_month' => SORT_ASC])->all();
        $dataCount = ServicePaymentFeeResponse::find()
            ->where(['apartment_id' => $apartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID])
            ->select(["SUM(price) as price","SUM(money_collected) as money_collected","SUM(more_money_collecte) as more_money_collecte"])->one();

        //        $file_path = '/uploads/fee/'.time().CVietnameseTools::stripSpecialChars($name_file).'-thong-tin-phi.xlsx';
        //        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $name_set = CVietnameseTools::stripSpecialChars($name_file).'-thong-tin-phi.xlsx';
        $path_set = '/uploads/fee/' . time().'-'.$name_set;
        $arr_file_fee = [
            'url' => $path_set,
            'name' => $name_set,
        ];
        $fileandpath = dirname((dirname(__DIR__))) . "/frontend/web".$path_set;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $arrColumns = ['A', 'B', 'C', 'D', 'E'];
        $i = 1;
        $spreadsheet->getActiveSheet()->mergeCells($arrColumns[0].$i.':E'.$i);
        $sheet->setCellValue('A'.$i, 'Căn hộ: ' . $apartment->name .', Lô: '.$lo.', Tầng: '.$tang);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFill()->getStartColor()->setARGB('e8e8e8');
        foreach ($arrColumns as $column){
        //            $w = 25;
        //            if($column == 'A'){
        //                $w = 10;
        //            }else if($column == 'C'){
        //                $w = 60;
        //            }
        //            $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth($w);
            $spreadsheet->getActiveSheet()->getStyle($column.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle($column.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }
        $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getAlignment()->setWrapText(true);


        $i++;
        $sheet->setCellValue($arrColumns[0].$i, 'STT');
        $sheet->setCellValue($arrColumns[1].$i, 'Loại dịch vụ');
        $sheet->setCellValue($arrColumns[2].$i, 'Chi tiết');
        $sheet->setCellValue($arrColumns[3].$i, 'Phí của tháng');
        $sheet->setCellValue($arrColumns[4].$i, 'Còn phải thanh toán (vnđ)');

        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFill()->getStartColor()->setARGB('10a54a');
        foreach ($arrColumns as $column){
            $w = 25;
            if($column == 'A'){
                $w = 10;
            }else if($column == 'C'){
                $w = 60;
            }
            $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth($w);
            $spreadsheet->getActiveSheet()->getStyle($column.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle($column.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }
        $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getAlignment()->setWrapText(true);

        $i++;
        foreach ($dataProviderModels as $item){
            $sheet->setCellValue('A'.$i, $i-1);
            $service_name = '';
            if($item->for_type == ServicePaymentFee::FOR_TYPE_1){
                $service_name = 'Đặt cọc - ';
            }else if($item->for_type == ServicePaymentFee::FOR_TYPE_2){
                $service_name = 'Phát sinh - ';
            }
            if (!empty($item->serviceMapManagement)) {
                $service_name .= $item->serviceMapManagement->service_name;
                if($item->type == ServicePaymentFee::TYPE_SERVICE_PARKING_FEE){
                    $serviceParkingFee = ServiceParkingFee::findOne(['service_payment_fee_id' => $item->id]);
                    if(!empty($serviceParkingFee)){
                        if(!empty($serviceParkingFee->serviceManagementVehicle)){
                            $service_name .= ' - BKS: ' . $serviceParkingFee->serviceManagementVehicle->number;
                        }
                    }
                }else{
                    //nếu phí từ book sẽ lấy thêm thông tin tiện ích
                    $booking = ServiceUtilityBooking::find()->where(['like', 'service_payment_fee_ids_text_search', ','.$item->id.','])->one();
                    if(!empty($booking)){
                        if(!empty($booking->serviceUtilityFree)){
                            $service_name .= ' - ' .$booking->serviceUtilityFree->name;
                        }
                    }
                }
            }
            $sheet->setCellValue('B'.$i, $service_name);
            $sheet->setCellValue('C'.$i, $item->description);
            $sheet->setCellValue('D'.$i, date('m/Y', $item->fee_of_month));
            $sheet->getStyle('D'.$i)
                ->getNumberFormat()
                ->setFormatCode('mm/yyyy');
            $sheet->setCellValue('E'.$i, $item->more_money_collecte);
            $sheet->getStyle('E'.$i)->getNumberFormat()->setFormatCode('#,##0');

            $i++;
        }
        $spreadsheet->getActiveSheet()->getStyle('C1:C'.$i)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('C1:C'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $spreadsheet->getActiveSheet()->getStyle('C1:C'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

        $spreadsheet->getActiveSheet()->mergeCells($arrColumns[0].$i.':D'.$i);
        $sheet->setCellValue('A'.$i, 'Tổng cộng (vnđ)');
        $sheet->setCellValue('E'.$i, (int)$dataCount->more_money_collecte);
        $sheet->getStyle('E'.$i)->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFill()->getStartColor()->setARGB('e8e8e8');
        $spreadsheet->getActiveSheet()->getRowDimension($i)->setRowHeight(25);

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return $arr_file_fee;
    }
    private function sendEmailToUserInHere($data){
        if(empty($data['email']))
        {
            return ;
        }
        $emailTos       = array_unique($data['email']);
        $subject        = $data['subject'];
        $contentHtml    = $data['contentHtml'];
        // Tạo một đối tượng Mailer
        $mailer = Yii::$app->mailer;

        // Thiết lập thông tin SMTP
        $mailer->transport = [
            'class' => 'Swift_SmtpTransport',
            'host' => 'mail.tanadaithanh.vn', // Thay thế bằng máy chủ SMTP thực tế
            'username' => 'Banquanly_MeyHomes@tanadaithanh.vn', // Thay thế bằng địa chỉ email của bạn
            'password' => 'oLthL35pKPZOe1p', // Thay thế bằng mật khẩu email của bạn
            'port' => 587, // Cổng SMTP thực tế
            'encryption' => 'tls', // Có thể sử dụng 'ssl' nếu cần
        ];
        print_r($emailTos);
        foreach ($emailTos as $key => $email_to){
            $message = $mailer->compose()
                ->setFrom(['Banquanly_MeyHomes@tanadaithanh.vn' => 'Tan A Dai Thanh']) // Địa chỉ email và tên người gửi
                ->setTo([$email_to => 'Tan A Dai Thanh']) // Địa chỉ email và tên người nhận
                ->setSubject($subject) // Tiêu đề email
                ->setHtmlBody($contentHtml) // Nội dung HTML của email
                ->setTextBody('Noi dung email mau'); // Nội dung văn bản thô (dành cho các máy khách không hỗ trợ HTML)
            if ($message->send()) {
                $is_send_email = 1;
            }
        }
            // Đính kèm tệp (nếu cần)
            // $message->attach('path/to/file.pdf');
    }
}
