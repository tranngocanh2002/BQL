<?php

namespace common\models;

use common\helpers\ErrorCode;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "resident_user_map_read".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $type 0 - yêu cầu, 1 - bản tin, 2 - thanh toán
 * @property int $is_read 0 - chưa đọc, 1 - đã đọc
 * @property int $created_at
 * @property int $updated_at
 * @property int $resident_user_id
 */
class ResidentUserMapRead extends \yii\db\ActiveRecord
{
    const IS_UNREAD = 0;
    const IS_READ = 1;

    const TYPE_REQUEST = 0;
    const TYPE_ANNOUNCEMENT = 1;
    const TYPE_PAYMENT_FEE = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resident_user_map_read';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'building_area_id', 'type', 'is_read', 'created_at', 'updated_at', 'resident_user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'building_area_id' => Yii::t('common', 'Building Area ID'),
            'type' => Yii::t('common', 'Type'),
            'is_read' => Yii::t('common', 'Is Read'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'resident_user_id' => Yii::t('common', 'Resident User ID'),
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
            ]
        ];
    }

    public static function updateOrCreate($data_set, $param_search, $residentUserIds)
    {
        Yii::info($param_search);
        Yii::info($data_set);
        Yii::info($residentUserIds);
        if(!isset($param_search['type']) || !isset($param_search['building_cluster_id'])){
            return false;
        }
        self::updateAll($data_set, $param_search);
        //tim các item chưa có thì tạo mới
        $items = self::find()->where($param_search)->all();
        foreach ($items as $item){
            unset($residentUserIds[$item->resident_user_id]);
        }
        foreach ($residentUserIds as $residentUserId){
            $item = self::findOne(['building_cluster_id' => $param_search['building_cluster_id'], 'type' => $param_search['type'], 'resident_user_id' => $residentUserId]);
            if(empty($item)){
                $item = new self();
                $item->building_cluster_id = $param_search['building_cluster_id'];
                $item->resident_user_id = $residentUserId;
                $item->type = $param_search['type'];
                Yii::error($param_search);
            }
            $item->is_read = ResidentUserMapRead::IS_UNREAD;
            if(!$item->save()){
                Yii::error($item->getErrors());
            }
        }
    }
}
