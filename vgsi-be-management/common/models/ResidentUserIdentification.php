<?php

namespace common\models;

use common\helpers\QueueLib;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "resident_user_identification".
 *
 * @property int $id
 * @property int $resident_user_id
 * @property int $building_cluster_id
 * @property int $status 0 - chưa xác thực, 1 - đã xác thực
 * @property string $medias
 * @property int $is_sync
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property ResidentUser $residentUser
 */
class ResidentUserIdentification extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const IS_SYNC = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resident_user_identification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resident_user_id', 'building_cluster_id'], 'required'],
            [['is_sync', 'resident_user_id', 'building_cluster_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['medias'], 'string'],
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
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'status' => Yii::t('common', 'Status'),
            'medias' => Yii::t('common', 'Medias'),
            'is_sync' => Yii::t('common', 'Is Sync'),
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

    public function sendFaceRecognition()
    {
//        "resident_user_id": 0,
//        "resident_user_name": "string",
//        "resident_user_gender": 0,
//        "status": 0,
//        "images": [
//            "string"
//        ]
        $payload = [
            'resident_user_id' => $this->resident_user_id,
            'resident_user_name' => '',
            'resident_user_gender' => 0,
            'status' => $this->status,
            'images' => [],
        ];
        if(!empty($this->residentUser)){
            $payload['resident_user_name'] = $this->residentUser->first_name;
            $payload['resident_user_gender'] = $this->residentUser->gender;
        }
        $link_api = Yii::$app->params['link_api'];
        if(!empty($this->medias)){
            $medias = json_decode($this->medias, true);
            if(!empty($medias['images'])){
                $res = [];
                foreach ($medias['images'] as $image){
                    $res[] = $link_api['web'] . $image;
                }
                $payload['images'] = $res;
            }
        }
        QueueLib::channelFaceRecognition(json_encode($payload), $this->building_cluster_id);
    }
}
