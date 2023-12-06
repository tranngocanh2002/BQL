<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementItem;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="CallbackForm")
 * )
 */
class CallbackForm extends Model
{
    /**
     * @SWG\Property(description="Id", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Status - trạng thái : 1 - thành công, 2 - Thất bại", default=1, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="Errors", default=1, type="string")
     * @var string
     */
    public $errors;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'required'],
            [['id', 'status'], 'integer'],
            [['errors'], 'safe'],
        ];
    }

    public function sms()
    {
        $item = AnnouncementItem::findOne(['id' => (int)$this->id]);
        if ($item) {
            $item->status_sms = $this->status;
            if(!empty($this->errors)){
                if(gettype($this->errors) !== 'string'){
                    $this->errors = json_encode($this->errors);
                }
                $item->errors_sms = $this->errors;
            }
            if (!$item->save()) {
                Yii::error($item->getErrors());
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            if($item->status_sms == AnnouncementItem::STATUS_SUCCESS){
                $announcementCampaign = AnnouncementCampaign::findOne(['id' => $item->announcement_campaign_id]);
                if(!empty($announcementCampaign)){
                    $announcementCampaign->total_sms_send_success++;
                    if($announcementCampaign->total_sms_send < $announcementCampaign->total_sms_send_success){
                        $announcementCampaign->total_sms_send = $announcementCampaign->total_sms_send_success;
                    }
                    if(!$announcementCampaign->save()){
                        Yii::error($announcementCampaign->errors);
                    }
                }
            }
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Update Success"),
            ];
        } else {
            Yii::error("Invalid data");
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function email()
    {
        $item = AnnouncementItem::findOne(['id' => (int)$this->id]);
        if ($item) {
            $item->status_email = $this->status;
            if(!empty($this->errors)){
                if(gettype($this->errors) !== 'string'){
                    $this->errors = json_encode($this->errors);
                }
                $item->errors_email = $this->errors;
            }
            if (!$item->save()) {
                Yii::error($item->getErrors());
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            if($item->status_email == AnnouncementItem::STATUS_SUCCESS){
                $announcementCampaign = AnnouncementCampaign::findOne(['id' => $item->announcement_campaign_id]);
                if(!empty($announcementCampaign)){
                    $announcementCampaign->total_email_send_success++;
                    if($announcementCampaign->total_email_send < $announcementCampaign->total_email_send_success){
                        $announcementCampaign->total_email_send = $announcementCampaign->total_email_send_success;
                    }
                    if(!$announcementCampaign->save()){
                        Yii::error($announcementCampaign->errors);
                    }
                }
            }
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Update Success"),
            ];
        } else {
            Yii::error("Invalid data");
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function notify()
    {
        $item = AnnouncementItem::findOne(['id' => (int)$this->id]);
        if ($item) {
            $item->status_notify = $this->status;
            if(!empty($this->errors)){
                if(gettype($this->errors) !== 'string'){
                    $this->errors = json_encode($this->errors);
                }
                $item->errors_notify = $this->errors;
            }
            if (!$item->save()) {
                Yii::error($item->getErrors());
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            if($item->status_notify == AnnouncementItem::STATUS_SUCCESS){
                $announcementCampaign = AnnouncementCampaign::findOne(['id' => $item->announcement_campaign_id]);
                if(!empty($announcementCampaign)){
                    if($announcementCampaign->total_app_success < $announcementCampaign->total_app_send){
                        $announcementCampaign->total_app_success++;
                        if(!$announcementCampaign->save()){
                            Yii::error($announcementCampaign->errors);
                        }
                    }
                }
            }
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Update Success"),
            ];
        } else {
            Yii::error("Invalid data");
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
