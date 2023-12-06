<?php



namespace common\models;

use frontend\models\AnnouncementSendNewResponse;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "announcement_item".
 *
 * @property int $id
 * @property int $announcement_campaign_id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $apartment_id
 * @property int $read_at
 * @property int $is_hidden
 * @property int $status   0 - đã gửi, 1 - thành công, 2 - thất bại
 * @property int $created_at
 * @property int $updated_at
 * @property int $announcement_item_send_id
 * @property int $read_email_at
 * @property int $type
 * @property int $end_debt
 * @property int $status_sms
 * @property int $status_email
 * @property int $status_notify
 * @property string $resident_user_name
 * @property string $phone
 * @property string $email
 * @property string $device_token
 * @property string $title
 * @property string $title_en
 * @property string $description
 * @property string $content
 * @property string $content_sms
 * @property string $errors_sms
 * @property string $errors_email
 * @property string $errors_notify
 *
 * @property AnnouncementCampaign $announcementCampaign
 * @property AnnouncementItemSend $announcementItemSend
 * @property Apartment $apartment
 */
class AnnouncementItem extends \yii\db\ActiveRecord
{
    const IS_NOT_HIDDEN = 0;
    const IS_HIDDEN = 1;

    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'announcement_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_notify', 'status_email', 'status_sms', 'read_email_at', 'type', 'end_debt', 'is_hidden', 'announcement_campaign_id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'read_at', 'status', 'created_at', 'updated_at', 'announcement_item_send_id'], 'integer'],
            [['device_token', 'phone', 'email', 'title', 'title_en', 'description', 'content', 'content_sms', 'resident_user_name', 'errors_sms', 'errors_email', 'errors_notify'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'announcement_campaign_id' => Yii::t('common', 'Announcement Campaign ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'apartment_id' => Yii::t('common', 'Apartment ID'),
            'read_at' => Yii::t('common', 'Read At'),
            'is_hidden' => Yii::t('common', 'Is Hidden'),
            'status' => Yii::t('common', 'Status'),
            'title' => Yii::t('common', 'Title'),
            'title_en' => Yii::t('common', 'Title En'),
            'description' => Yii::t('common', 'Description'),
            'content' => Yii::t('common', 'Content'),
            'content_sms' => Yii::t('common', 'Content Sms'),
            'announcement_item_send_id' => Yii::t('common', 'Announcement Item Send Id'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'time',
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ]
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnnouncementCampaign()
    {
        return $this->hasOne(AnnouncementCampaign::className(), ['id' => 'announcement_campaign_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::className(), ['id' => 'apartment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnnouncementItemSend()
    {
        return $this->hasOne(AnnouncementItemSend::className(), ['id' => 'announcement_item_send_id']);
    }

    public static function countTotalSend($building_cluster_id, $start_time = null, $end_time = null){
        if(empty($start_time)){
            $start_time = strtotime(date('Y-m-01 00:00:00', time()));
        }
        if(empty($end_time)){
            $end_time = strtotime(date('Y-m-t 23:59:59', time()));
        }

        $total_email = self::find()->where(['building_cluster_id' => $building_cluster_id])
            ->andWhere(['>=', 'created_at', $start_time])
            ->andWhere(['<=', 'created_at', $end_time])
            ->andWhere(['<>', 'email', ''])
            ->andWhere(['not', ['email' => null]])->count();

        $total_app = self::find()->where(['building_cluster_id' => $building_cluster_id])
            ->andWhere(['>=', 'created_at', $start_time])
            ->andWhere(['<=', 'created_at', $end_time])
            ->andWhere(['<>', 'device_token', ''])
            ->andWhere(['not', ['device_token' => null]])->count();

        $total_phone = self::find()->where(['building_cluster_id' => $building_cluster_id])
            ->andWhere(['>=', 'created_at', $start_time])
            ->andWhere(['<=', 'created_at', $end_time])
            ->andWhere(['<>', 'phone', ''])
            ->andWhere(['not', ['phone' => null]])->count();

        return [
            'total_email' => (int)$total_email,
            'total_app' => (int)$total_app,
            'total_sms' => (int)$total_phone,
        ];
    }

    public static function totalSendEmail($building_cluster_id, $building_area_ids, $targets){
        $query_count = AnnouncementSendNewResponse::find()
        ->where([
            'building_cluster_id' => $building_cluster_id,
            'is_deleted' => Apartment::NOT_DELETED,

        ])
        ->andWhere(['IN', 'building_area_id' , $building_area_ids])
        ->andWhere(['IN', 'type' , $targets])
        ->andWhere(['<>', 'resident_user_email', ''])
        ->andWhere(['not', ['resident_user_email' => null]])
        ->count('DISTINCT resident_user_email');
        return $query_count ; 
    }
}
