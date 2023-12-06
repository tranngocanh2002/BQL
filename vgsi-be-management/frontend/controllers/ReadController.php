<?php

namespace frontend\controllers;

use common\helpers\CUtils;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementItem;
use common\models\AnnouncementTemplate;
use common\models\Apartment;
use common\models\ServiceBuildingConfig;
use common\models\ServicePaymentFee;
use Exception;
use frontend\models\PdfTemplateCreateForm;
use Yii;
use yii\base\UserException;
use yii\rest\Controller;
use yii\web\HttpException;

class ReadController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'email',
            'pdf',
            'fee',
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionEmail($code)
    {
        Yii::info($code);
        $announcementItem = AnnouncementItem::findOne(['id' => (int)$code]);
        if (!empty($announcementItem)) {
            if (empty($announcementItem->read_email_at)) {
                $announcementItem->read_email_at = time();
                $announcementCampaign = AnnouncementCampaign::findOne(['id' => $announcementItem->announcement_campaign_id]);
                if (!empty($announcementCampaign)) {
                    $announcementCampaign->total_email_open++;
                    if($announcementCampaign->total_email_send_success < $announcementCampaign->total_email_send){
                        $announcementCampaign->total_email_send_success = $announcementCampaign->total_email_send;
                    }
                    if (!$announcementItem->save() || !$announcementCampaign->save()) {
                        Yii::error($announcementItem->errors);
                        Yii::error($announcementCampaign->errors);
                    }
                }
            }
        }
        die("data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
    }

    public function actionPdf($apartment_id, $campaign_type)
    {
        $this->layout = 'print';

//        $model = new PdfTemplateCreateForm();
//        $model->apartment_id = (int)$apartment_id;
//        $model->campaign_type = (int)$campaign_type;
//        die($model->gen());

//        $apartment = Apartment::findOne(['id' => (int)$apartment_id]);
//        $content_pdf = $this->render('/pdf/fee_new_db', ['apartment' => $apartment, 'campaign_type' => (int)$campaign_type]);
//        die($content_pdf);

        $apartment = Apartment::findOne(['id' => (int)$apartment_id]);
        $content_pdf = $this->render('/pdf/all_service_fee', ['apartment' => $apartment]);
//        echo html_entity_decode(htmlentities($content_pdf));
        $m = new \Mustache_Engine();
        die($m->render($content_pdf, [])); // "Hello, World!"
    }

    public function actionFee($apartment_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        $this->layout = 'print';
        $apartment = Apartment::findOne(['id' => (int)$apartment_id]);
        return $this->render('/pdf/all_service_fee', ['apartment' => $apartment]);
    }
}
