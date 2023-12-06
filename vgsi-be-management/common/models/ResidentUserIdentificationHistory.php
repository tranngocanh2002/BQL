<?php

namespace common\models;

use common\helpers\NotificationTemplate;
use common\helpers\OneSignalApi;
use common\helpers\QueueLib;
use common\helpers\SocketHelper;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "resident_user_identification_history".
 *
 * @property int $id
 * @property int $resident_user_id
 * @property int $type 0 - là nhận diện cư dân, 1 - người lạ
 * @property int $time_event
 * @property string $image_name
 * @property string $image_uri
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property ResidentUser $residentUser
 */
class ResidentUserIdentificationHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resident_user_identification_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resident_user_id', 'type', 'time_event', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['image_name', 'image_uri'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'resident_user_id' => Yii::t('common', 'Resident User ID'),
            'type' => Yii::t('common', 'Type'),
            'time_event' => Yii::t('common', 'Time Event'),
            'image_name' => Yii::t('common', 'Image Name'),
            'image_uri' => Yii::t('common', 'Image Uri'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'created_by' => Yii::t('common', 'Created By'),
            'updated_by' => Yii::t('common', 'Updated By'),
        ];
    }

    /**
     * @inheritdoc
     */
    function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'time',
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ]
            ],
            [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    self::EVENT_BEFORE_UPDATE => ['updated_by'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ],
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResidentUser()
    {
        return $this->hasOne(ResidentUser::className(), ['id' => 'resident_user_id']);
    }

    public function sendNotify(){
        /*
         * chuyển tiếp bản tin xuống WEB/APP
         * TODO: Tạm thời gửi cho tòa demo, sau update cơ ché sync device thì update ở đây
         */
        $buildingCluster = BuildingCluster::find()->where(['is_deleted' => BuildingCluster::NOT_DELETED, 'id' => 6])->one();
        $payload = [
            'cmd' => 'event',
            'reqid' => '',
            'objects' => [
                [
                    'type' => 'identification',
                    'data' => [
                        'resident_user_id' => $this->resident_user_id,
                        'resident_user_name' => !empty($this->residentUser) ? $this->residentUser->first_name : '',
                        'type' => $this->type,
                        'time_event' => $this->time_event,
                        'image_name' => $this->image_name,
                        'image_uri' => $this->image_uri,
                    ]
                ]
            ]
        ];
        $socket = new SocketHelper();
        $socket->of('/')->to('building_cluster_' . $buildingCluster->id)->flag('broadcast')->emit('status', ['payload' => $payload]);
        Yii::info($socket->res());
//        if($buildingCluster->security_mode !== BuildingCluster::SECURITY_MODE){
//            return;
//        }
        $managementUsers = ManagementUser::find()->where(['building_cluster_id' => $buildingCluster->id])->all();
        //gửi thông báo notify tới web ban quản lý
//        $title = 'Cảnh báo có người lạ khu vực sảnh A1 luc';
        $title = $title_en = 'Cảnh báo có người lạ tại khu vực sảnh A1 lúc ' . date('H:i:s d/m/Y', $this->time_event);
        $description = $description_en = $title;
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

            //gửi thông báo cho các user thuộc nhóm quyền này biết có phí cần duyệt
            $oneSignalApi = new OneSignalApi();
            //gửi thông báo theo device token
            $player_ids = [];
            foreach ($managementUser->managementUserDeviceTokens as $managementUserDeviceToken) {
                $player_ids[] = $managementUserDeviceToken->device_token;
            }
            $data['management_user_id'] = $managementUser->id;
            $oneSignalApi->sendToWorkerPlayerIds($title, $description, $title_en, $description_en, $player_ids, $data, null, $app_id);
            //end gửi thông báo theo device token
        }

        if($buildingCluster->security_mode === BuildingCluster::SECURITY_MODE){
            //gửi tin nhắn tới thanh viên ban quản lý
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
}
