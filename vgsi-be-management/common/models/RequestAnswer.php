<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "request_answer".
 *
 * @property int $id
 * @property int $request_id
 * @property int $resident_user_id
 * @property int $management_user_id
 * @property string $content
 * @property string $attach
 * @property int $is_deleted 0 : chưa xóa, 1 : đã xóa
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Request $request
 * @property ResidentUser $residentUser
 * @property ApartmentMapResidentUser $apartmentMapResidentUser
 * @property ManagementUser $managementUser
 */
class RequestAnswer extends \yii\db\ActiveRecord
{
    const NOT_DELETED = 0;
    const DELETED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'resident_user_id', 'management_user_id', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['content', 'attach'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'request_id' => Yii::t('common', 'Request ID'),
            'resident_user_id' => Yii::t('common', 'Resident User ID'),
            'management_user_id' => Yii::t('common', 'Management User ID'),
            'content' => Yii::t('common', 'Content'),
            'attach' => Yii::t('common', 'Attach'),
            'is_deleted' => Yii::t('common', 'Is Deleted'),
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
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResidentUser()
    {
        return $this->hasOne(ResidentUser::className(), ['id' => 'resident_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequest()
    {
        return $this->hasOne(Request::className(), ['id' => 'request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagementUser()
    {
        return $this->hasOne(ManagementUser::className(), ['id' => 'management_user_id']);
    }

    function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        //tăng total answer vào request
        if ($insert == true) {
            $request = Request::findOne(['id' => $this->request_id]);
            $request->total_answer += 1;
            $request->save();
        }
    }

    public function getApartmentMapResidentUser()
    {
        return ApartmentMapResidentUser::findOne([
            'apartment_id' => $this->request->apartment_id,
            'resident_user_phone' => $this->residentUser->phone,
            'is_deleted' => ApartmentMapResidentUser::NOT_DELETED,
        ]);
    }
}
