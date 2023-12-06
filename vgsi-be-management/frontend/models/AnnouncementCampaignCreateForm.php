<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\helpers\ErrorCode;
use common\helpers\StringUtils;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementCategory;
use common\models\AnnouncementItem;
use common\models\AnnouncementItemSend;
use common\models\AnnouncementTemplate;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingArea;
use common\models\ServiceDebt;
use frontend\models\AnnouncementItemSearch;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementCampaignCreateForm")
 * )
 */
class AnnouncementCampaignCreateForm extends Model
{
    /**
     * @SWG\Property(description="Id - Bắt buộc khi update", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Title")
     * @var string
     */
    public $title;

    /**
     * @SWG\Property(description="Title En")
     * @var string
     */
    public $title_en;

    /**
     * @SWG\Property(description="Content: chèn thêm key thay thế : căn hộ => {{APARTMENT_NAME}}, tên chủ hộ => {{RESIDENT_NAME}}, tổng phí => {{TOTAL_FEE}}, bảng danh sách phí => {{TABLE_ALL_FEE}}")
     * @var string
     */
    public $content;

    /**
     * @SWG\Property(property="attach", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $attach;

    /**
     * @SWG\Property(property="building_area_ids", type="array",
     *     @SWG\Items(type="integer", default=1),
     * ),
     * @var array
     */
    public $building_area_ids;

    /**
     * @SWG\Property(property="apartment_ids", type="array", description="Danh sách id apartment sẽ gửi thông báo, nếu tồi tại sẽ ưu tiên gửi theo dánh sách này, không gửi theo building_area_ids nữa",
     *     @SWG\Items(type="integer", default=1),
     * ),
     * @var array
     */
    public $apartment_ids;

    /**
     * @SWG\Property(property="apartment_send_ids", type="array", description="Danh sách id apartment sẽ gửi thông báo, nếu tồi tại sẽ ưu tiên gửi theo dánh sách này, không gửi theo building_area_ids nữa",
     *     @SWG\Items(type="integer", default=1),
     * ),
     * @var array
     */
    public $apartment_send_ids;

    /**
     * @SWG\Property(property="apartment_not_send_ids", type="array", description="danh sách id apartment sẽ không gửi thông báo",
     *     @SWG\Items(type="integer", default=1),
     * ),
     * @var array
     */
    public $apartment_not_send_ids;

    /**
     * @SWG\Property(property="add_phone_send", type="array", description="Những số điện thoại sẽ gửi thông báo thêm",
     *     @SWG\Items(type="string", default=""),
     * ),
     * @var array
     */
    public $add_phone_send;

    /**
     * @SWG\Property(property="add_email_send", type="array", description="Những email sẽ gửi thông báo thêm",
     *     @SWG\Items(type="string", default=""),
     * ),
     * @var array
     */
    public $add_email_send;

    /**
     * @SWG\Property(description="Status - trạng thái : 0 - lưu nháp, 1 - gửi luôn", default=1, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="Send At - Thời điểm gửi thông báo : mặc định là thời điểm hiện tại", type="integer")
     * @var integer
     */
    public $send_at;

    /**
     * @SWG\Property(description="announcement category id", default=1, type="integer")
     * @var integer
     */
    public $announcement_category_id;

    /**
     * @SWG\Property(description="is event : 1 - là tin sự kiện (sẽ có thông báo gửi trước x phút), 0 - không phải sự kiện", default=0, type="integer")
     * @var integer
     */
    public $is_event;

    /**
     * @SWG\Property(description="send event at : Thời gian gửi sự kiện", default=0, type="integer")
     * @var integer
     */
    public $send_event_at;

    /**
     * @SWG\Property(description="is survey : 1 - là thông báo khảo sát, 0 - không phải thông báo khảo sát", default=0, type="integer")
     * @var integer
     */
    public $is_survey;

    /**
     * @SWG\Property(description="survey deadline : Thời hạn làm khảo sát", default=0, type="integer")
     * @var integer
     */
    public $survey_deadline;

    /**
     * @SWG\Property(description="type_report kiểu báo cáo 0- tính theo diện tích, 1 - tính theo đầu người", default=0, type="integer")
     * @var integer
     */
    public $type_report;

    /**
     * @SWG\Property(property="targets", type="array", description="đối tượng nhận thông báo [0,1,2, ...]: 0 - Gia đình chủ hộ, 1 - chủ hộ, 2 - khách thuê, 3 - Gia đình khách thuê",
     *     @SWG\Items(type="integer", default=1),
     * ),
     * @var array
     */
    public $targets;

    /**
     * @SWG\Property(property="resident_user_phones", type="array", description="đối tượng không nhận thông báo [489xxxx,8496xxx,8498xxx, ...]: số điện thoại của cư dân",
     *     @SWG\Items(type="integer", default=1),
     * ),
     * @var array
     */
    public $resident_user_phones;

    /**
     * @SWG\Property(description="0: ko gửi notify, 1 có gửi notify", default=0, type="integer")
     * @var integer
     */
    public $is_send_push;

    /**
     * @SWG\Property(description="0: ko gửi email, 1 có gửi email", default=0, type="integer")
     * @var integer
     */
    public $is_send_email;

    /**
     * @SWG\Property(description="0: ko gửi sms, 1 có gửi sms", default=0, type="integer")
     * @var integer
     */
    public $is_send_sms;

    /**
     * @SWG\Property(description="Content Sms : chèn thêm key thay thế : căn hộ => {{APARTMENT_NAME}}, tên chủ hộ => {{RESIDENT_NAME}}, tổng phí => {{TOTAL_FEE}}")
     * @var string
     */
    public $content_sms;

    /**
     * @SWG\Property(description="Loại thông báo : 0 - thông báo thường, 1 - thông báo phí, 2 nhắc nợ lần 1 , 3 - nhắc nợ lần 2, 4 - nhắc nợ lần 3, 5 - thông báo tạm dừng dịch vụ", default=0, type="integer")
     * @var integer
     */
    public $type;

    public $description;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'announcement_category_id'], 'required'],
            [['title', 'title_en', 'description', 'content', 'content_sms'], 'string'],
            [['id', 'type_report', 'announcement_category_id', 'send_at', 'status', 'is_event', 'send_event_at', 'type', 'is_send_push', 'is_send_email', 'is_send_sms', 'is_survey', 'survey_deadline'], 'integer'],
            [['announcement_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnnouncementCategory::className(), 'targetAttribute' => ['announcement_category_id' => 'id']],
            [['attach', 'building_area_ids', 'apartment_ids','apartment_send_ids', 'apartment_not_send_ids', 'add_email_send', 'add_phone_send', 'targets', 'resident_user_phones'], 'safe'],
            [['id'], 'required', "on" => ['update']],
//            [['id'], 'integer', "on" => ['update']],
            [['add_email_send'], 'validateEmailSend'],
            [['add_phone_send'], 'validatePhoneSend'],
        ];
    }

    public function validatePhoneSend($attribute, $params, $validator)
    {
        if(is_array($this->add_phone_send)){
            foreach ($this->add_phone_send as $phone){
                Yii::warning($phone);
                $phone = CUtils::validateMsisdn($phone);
                if (empty($phone)) {
                    $this->addError($attribute, Yii::t('frontend', 'Số điện thoại gửi thêm không đúng định dạng'));
                }else{
                    Yii::warning('xxxx');
                    Yii::warning($phone);
                }
            }
        }else{
            Yii::warning('not array phone');
        }
    }

    public function validateEmailSend($attribute, $params, $validator)
    {
        if(is_array($this->add_email_send)){
            foreach ($this->add_email_send as $email){
                $isEmail = new ValidateEmailForm();
                $isEmail->email = $email;
                if (!$isEmail->validate()) {
                    $this->addError($attribute, Yii::t('frontend', 'Email gửi thêm không đúng định dạng'));
                }
            }
        }else{
            Yii::warning('not array');
        }
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $buildingCluster = Yii::$app->building->BuildingCluster;
            //xử lý trường hợp tạo mới mà có id truyền lên thì tức là update
            //thực hiện xóa id cũ, và tạo mới như bình thường
            if(isset($this->id) && !empty($this->id)){
                $campaignOld = AnnouncementCampaign::findOne(['id' => $this->id, 'status' => AnnouncementCampaign::STATUS_UNACTIVE]);
                if(!empty($campaignOld)){
                    AnnouncementItem::deleteAll(['announcement_campaign_id' => $campaignOld->id]);
                    AnnouncementItemSend::deleteAll(['announcement_campaign_id' => $campaignOld->id]);
                    if(!$campaignOld->delete()){
                        Yii::error($campaignOld->errors);
                    }
                }
                $this->id = null;
            }
            //chặn gửi liên tiếp nhắc nợ
            if($this->type > 0 && $this->status == AnnouncementCampaign::STATUS_ACTIVE){
                $announcementCampaign =  AnnouncementCampaign::find()->where(['building_cluster_id' => $buildingCluster->id, 'type' => $this->type, 'status' => AnnouncementCampaign::STATUS_ACTIVE])->orderBy(['id' => SORT_DESC])->one();
                if(!empty($announcementCampaign)){
                    if(($announcementCampaign->is_send == AnnouncementCampaign::IS_UNSEND && (($announcementCampaign->send_at + 5*60) > time())) || empty($announcementCampaign->send_at) || ($announcementCampaign->is_send == AnnouncementCampaign::IS_SEND && (($announcementCampaign->send_at + 5*60) > time()))){
                        $transaction->rollBack();
                        $tname = 'gửi';
                        if($this->type > 1){
                            $tname .= ' nhắc nợ';
                        }
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Chờ sau 5 phút cho lần ".$tname." tiếp theo"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        ];
                    }
                }
            }
            //kiểm tra xem mẫu file pdf đã tồn tại hay chưa
//            if ($this->type > 0) {
//                $announcementTemplate = AnnouncementTemplate::findOne(['building_cluster_id' => $buildingCluster->id, 'type' => $this->type]);
//                if (empty($announcementTemplate)) {
//                    $transaction->rollBack();
//                    return [
//                        'success' => false,
//                        'message' => Yii::t('frontend', "Announcement Template Invalid"),
//                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//                    ];
//                }
//            }
            //lấy số lượng đã gửi
            $countTotalSend = AnnouncementItem::countTotalSend($buildingCluster->id);

            $item = new AnnouncementCampaign();
            $item->load(CUtils::arrLoad($this->attributes), '');

            $item->description = CVietnameseTools::truncateString(strip_tags($item->content), 100);
            $item->building_cluster_id = $buildingCluster->id;
            if (empty($item->send_at)) {
                $item->send_at = time();
            }
            $targets_fitlter_email = []; 
            if (isset($this->targets) && is_array($this->targets)) {
                $targets_fitlter_email = $this->targets ; 
                $item->targets = !empty($this->targets) ? Json::encode($this->targets) : null;
            }
            if (isset($this->resident_user_phones) && is_array($this->resident_user_phones)) {
                $item->resident_user_phones = !empty($this->resident_user_phones) ? Json::encode($this->resident_user_phones) : null;
            }
            if (isset($this->attach) && is_array($this->attach)) {
                $item->attach = !empty($this->attach) ? Json::encode($this->attach) : null;
            }
            if (isset($this->apartment_ids) && is_array($this->apartment_ids)) {
                // $item->apartment_ids = !empty($this->apartment_ids) ? Json::encode($this->apartment_ids) : null;
                $item->apartment_ids = !empty($this->apartment_ids) ? Json::encode($this->apartment_ids) : Json::encode($this->apartment_send_ids);
            }
            if (isset($this->apartment_not_send_ids) && is_array($this->apartment_not_send_ids)) {
                $item->apartment_not_send_ids = !empty($this->apartment_not_send_ids) ? Json::encode($this->apartment_not_send_ids) : null;
            }
            $add_phone_count = 0;
            if (isset($this->add_phone_send) && is_array($this->add_phone_send)) {
                $add_phone_count = count($this->add_phone_send);
                $item->add_phone_send = !empty($this->add_phone_send) ? Json::encode($this->add_phone_send) : null;
            }
            $add_email_count = 0;
            if (isset($this->add_email_send) && is_array($this->add_email_send)) {
                $add_email_count = count($this->add_email_send);
                $item->add_email_send = !empty($this->add_email_send) ? Json::encode($this->add_email_send) : null;
                if(isset($this->is_send_email) && $this->is_send_email == 1){
                    $count = AnnouncementItem::totalSendEmail($buildingCluster->id, $this->building_area_ids, $targets_fitlter_email);
                    $item->total_email_send =  $count ; 
                }
            }
            
            if(empty($this->type_report)){
                $item->type_report = AnnouncementCampaign::TYPE_REPORT_DEFAULT;
            }
            // lấy danh sách gửi email thông báo
            // $arySendEmailNew = $this->sendEmailNew();
            // $item->total_email_send = (int)$arySendEmailNew['countEmailSend'];
            // $item->total_email_send_success = (int)$arySendEmailNew['countEmailSendSucess'];
            if (!$item->save()) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            if (!empty($this->building_area_ids) && is_array($this->building_area_ids) && empty($this->apartment_ids)) {
                // số lượng sẽ gửi thông báo
                $countTotalSendCampaign = AnnouncementCampaign::countTotalSend($buildingCluster->id, $this->building_area_ids);
                if (
                    ($this->is_send_push == AnnouncementCampaign::IS_SEND_PUSH && $buildingCluster->limit_notify < ($countTotalSend['total_app'] + $countTotalSendCampaign['total_app']))
                    || ($this->is_send_email == AnnouncementCampaign::IS_SEND_EMAIL && $buildingCluster->limit_email < ($countTotalSend['total_email'] + $countTotalSendCampaign['total_email'] + $add_email_count))
                    || ($this->is_send_sms == AnnouncementCampaign::IS_SEND_SMS && $buildingCluster->limit_sms < ($countTotalSend['total_sms'] + $countTotalSendCampaign['total_sms'] + $add_phone_count))
                ) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Over the limit allowed to be sent in the month"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                }
                if(!$this->type){
                    foreach ($this->building_area_ids as $building_area_id) {
                        $announcementItemSend = new AnnouncementItemSend();
                        $announcementItemSend->announcement_campaign_id = $item->id;
                        $announcementItemSend->building_cluster_id = $item->building_cluster_id;
                        $announcementItemSend->building_area_id = (int)$building_area_id;
                        $announcementItemSend->type = $item->type;
                        $announcementItemSend->tags = 'BUILDING_' . $building_area_id;
                        if (!$announcementItemSend->save()) {
                            $transaction->rollBack();
                            return [
                                'success' => false,
                                'message' => Yii::t('frontend', "Invalid data"),
                                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                                'errors' => $announcementItemSend->getErrors()
                            ];
                        }
                    }
                }
                
            }

            //thuộc loại thông báo nhắc nợ thì sẽ lấy các căn hộ đang nợ ra
            if (in_array($this->type, [AnnouncementCampaign::TYPE_REMINDER_DEBT_1, AnnouncementCampaign::TYPE_REMINDER_DEBT_2, AnnouncementCampaign::TYPE_REMINDER_DEBT_3, AnnouncementCampaign::TYPE_REMINDER_DEBT_4, AnnouncementCampaign::TYPE_REMINDER_DEBT_5])) {
                //số lượng sẽ gửi nhắc nợ
                $countTotalSendDebt = ServiceDebt::countTotalSend($buildingCluster->id, $this->type);
                if (
                    ($this->is_send_push == AnnouncementCampaign::IS_SEND_PUSH && $buildingCluster->limit_notify < ($countTotalSend['total_app'] + $countTotalSendDebt['total_app']))
                    || ($this->is_send_email == AnnouncementCampaign::IS_SEND_EMAIL && $buildingCluster->limit_email < ($countTotalSend['total_email'] + $countTotalSendDebt['total_email'] + $add_email_count))
                    || ($this->is_send_sms == AnnouncementCampaign::IS_SEND_SMS && $buildingCluster->limit_sms < ($countTotalSend['total_sms'] + $countTotalSendDebt['total_sms'] + $add_phone_count))
                ) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Over the limit allowed to be sent in the month"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                }

                $serviceDebts = ServiceDebt::find()->where(['building_cluster_id' => $buildingCluster->id, 'type' => ServiceDebt::TYPE_CURRENT_MONTH])
//                    ->andWhere(['>', 'status', ServiceDebt::STATUS_PAID])
                      ->andWhere(['status' => $this->type]);
                if(!empty($this->apartment_not_send_ids) && is_array($this->apartment_not_send_ids)){
                    $serviceDebts = $serviceDebts->andWhere(['not', ['apartment_id' => $this->apartment_not_send_ids]]);
                }
                if(!empty($this->apartment_ids) && is_array($this->apartment_ids)){
                    $serviceDebts = $serviceDebts->andWhere(['apartment_id' => $this->apartment_ids]);
                }
                if($this->type){
                    $serviceDebts = $serviceDebts->andWhere(['building_area_id' => $this->building_area_ids]);
                }
                $serviceDebts = $serviceDebts->all();
                if(empty($serviceDebts)){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Không còn căn hộ thuộc trạng thái nhắc nợ này"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                }
                $building_area_id = []; 
                $apartment_id = []; 
                foreach ($serviceDebts as $serviceDebt) {
                    $announcementItemSend = new AnnouncementItemSend();
                    $announcementItemSend->announcement_campaign_id = $item->id;
                    $announcementItemSend->building_cluster_id = $item->building_cluster_id;
                    $announcementItemSend->building_area_id = $serviceDebt->building_area_id;
                    $announcementItemSend->apartment_id = $serviceDebt->apartment_id;
                    $apartment_id[] = $serviceDebt->apartment_id ; 
                    $building_area_id[] = $serviceDebt->building_area_id ; 
                    $announcementItemSend->type = $item->type;
                    $announcementItemSend->end_debt = $serviceDebt->end_debt;
                    $announcementItemSend->tags = 'APARTMENT_' . $serviceDebt->apartment_id;
                    if (!$announcementItemSend->save()) {
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                            'errors' => $announcementItemSend->getErrors()
                        ];
                    }
                    $serviceDebt->status = (int)$this->type + 1 ;
                    if (!$serviceDebt->save()) {
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                            'errors' => $announcementItemSend->getErrors()
                        ];
                    }
                }
                if(isset($this->is_send_email) && $this->is_send_email == 1){
                    $query = ApartmentMapResidentUser::find()->where(['apartment_id' => $apartment_id, 'building_cluster_id' => $buildingCluster->id, 'building_area_id'=> $building_area_id, 'is_deleted' => Apartment::NOT_DELETED])
                    ->andWhere(['IN', 'type' , $targets_fitlter_email])
                    ->andWhere(['<>', 'resident_user_email', ''])
                    ->andWhere(['not', ['resident_user_email' => null]])
                    ->count('DISTINCT resident_user_email');
                    $item->total_email_send =  $query ; 
                    if (!$item->save()) {
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                            'errors' => $item->getErrors()
                        ];
                    }
            }
                
            }else if(!empty($this->apartment_ids) && is_array($this->apartment_ids)){
                $apartments = Apartment::find()->where(['id' => $this->apartment_ids, 'building_cluster_id' => $buildingCluster->id, 'status' => Apartment::STATUS_LIVE, 'is_deleted' => Apartment::NOT_DELETED])->all();
                if(empty($apartments)){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Căn hộ gửi thông báo không phù hợp"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                }
                foreach ($apartments as $apartment){
                    $announcementItemSend = new AnnouncementItemSend();
                    $announcementItemSend->announcement_campaign_id = $item->id;
                    $announcementItemSend->building_cluster_id = $apartment->building_cluster_id;
                    $announcementItemSend->building_area_id = $apartment->building_area_id;
                    $announcementItemSend->apartment_id = $apartment->id;
                    $announcementItemSend->type = $item->type;
                    $announcementItemSend->tags = 'APARTMENT_' . $apartment->id;
                    if (!$announcementItemSend->save()) {
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                            'errors' => $announcementItemSend->getErrors()
                        ];
                    }
                }
            }
            //Lưu action log nên công khai thông báo
            if ($this->status == AnnouncementCampaign::STATUS_ACTIVE) {
                $actionLog = new ActionLogForm();
                $actionLog->action = 'active_status';
                $actionLog->create();
            }

            $transaction->commit();
            $res = AnnouncementCampaignResponse::findOne(['id' => (int)$item->id]);
            if($res->status == AnnouncementCampaign::STATUS_ACTIVE){
                $is_nam_long = Yii::$app->params['is_nam_long'];
                if($is_nam_long == true){
                    $path_root_command = Yii::$app->params['path_root_command'];
                    $index = $item->id%2;
                    $output = shell_exec($path_root_command.' php yii announcement/push 2 '.$index);
                    Yii::warning($output);
                }
            }
            return $res;
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function update()
    {
        $item = AnnouncementCampaignResponse::findOne(['id' => (int)$this->id]);
        if ($item) {
            $item->load(CUtils::arrLoad($this->attributes), '');
            $item->description = CVietnameseTools::truncateString(strip_tags($item->content), 100);
            if (isset($this->targets) && is_array($this->targets)) {
                $item->targets = !empty($this->targets) ? Json::encode($this->targets) : null;
            }
            if (isset($this->resident_user_phones) && is_array($this->resident_user_phones)) {
                $item->resident_user_phones = !empty($this->resident_user_phones) ? Json::encode($this->resident_user_phones) : null;
            }
            if (isset($this->attach) && is_array($this->attach)) {
                $item->attach = !empty($this->attach) ? Json::encode($this->attach) : null;
            }
            if ($item->status == AnnouncementCampaign::STATUS_ACTIVE && $item->is_send == AnnouncementCampaign::IS_UNSEND) {
                if (empty($item->send_at)) {
                    $item->send_at = time();
                }
                //Lưu action log nên công khai thông báo
                $actionLog = new ActionLogForm();
                $actionLog->action = 'active_status';
                $actionLog->create();
            }
            $add_phone_count = 0;
            if (isset($this->add_phone_send) && is_array($this->add_phone_send)) {
                $add_phone_count = count($this->add_phone_send);
                $item->add_phone_send = !empty($this->add_phone_send) ? Json::encode($this->add_phone_send) : null;
            }
            $add_email_count = 0;
            if (isset($this->add_email_send) && is_array($this->add_email_send)) {
                $add_email_count = count($this->add_email_send);
                $item->add_email_send = !empty($this->add_email_send) ? Json::encode($this->add_email_send) : null;
            }
            if(empty($item->type_report)){
                $item->type_report = AnnouncementCampaign::TYPE_REPORT_DEFAULT;
            }
            if (!$item->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            if ($item->status == AnnouncementCampaign::STATUS_ACTIVE && $item->is_send == AnnouncementCampaign::IS_UNSEND) {
                $is_nam_long = Yii::$app->params['is_nam_long'];
                if($is_nam_long == true){
                    $path_root_command = Yii::$app->params['path_root_command'];
                    $index = $item->id%2;
                    $output = shell_exec($path_root_command.' php yii announcement/push 2 '.$index);
                    Yii::warning($output);
                }
            }
            return $item;
        } else {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function sendEmailNew()
    {
        $AnnouncementItemSearch = new AnnouncementItemSearch();
        $aryIdApartment = $this->apartment_ids;
        if(1 == $this->type)
        {
            $aryIdApartment = $this->apartment_send_ids;
        }
        if(empty($aryIdApartment))
        {
            $apartments = Apartment::find()->where(['building_area_id' => $this->building_area_ids])->all();
            if(!empty($apartments))
            {
                foreach($apartments as $apartment )
                {
                    $aryIdApartment[] = $apartment->id;
                }
            }
        }
        $data = $AnnouncementItemSearch->searchByIdAparmentMapResidentUser($aryIdApartment,$this->targets);
        $dataResults = [];
        $residentUserPhones = $this->resident_user_phones;
        $aryEmailList = [];
        foreach($data as $itemValue)
        {
            $dataResults[$itemValue->resident_user_phone] = $itemValue->resident_user_email;
        }
        foreach($dataResults as $key => $value)
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
        $aryEmailList = array_unique($aryEmailList);
        $countEmailSend = count($aryEmailList);
        $countEmailSendSucess = 0 ;
        if($this->is_send_email)
        {
            foreach($aryEmailList as $dataResult){
                if(empty($dataResult))
                {
                    continue;
                }
                Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'news'],
                    [
                        'data' => $this->content
                    ]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => 'Support Luci'])
                ->setTo($dataResult)
                ->setSubject($this->title)
                ->send();
                $countEmailSendSucess += 1;
            }
        }
        return [
            'countEmailSend' => $countEmailSend,
            'countEmailSendSucess' => $countEmailSendSucess
        ];
    }
}
