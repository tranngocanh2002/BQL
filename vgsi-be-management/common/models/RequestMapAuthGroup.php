<?php

namespace common\models;

use common\helpers\OneSignalApi;
use common\models\rbac\AuthGroup;
use Yii;

/**
 * This is the model class for table "request_map_auth_group".
 *
 * @property int $auth_group_id
 * @property int $request_id
 *
 * @property ManagementUser[] $managementUsers
 * @property Request $request
 * @property AuthGroup $authGroup
 */
class RequestMapAuthGroup extends \yii\db\ActiveRecord
{
    const NOT_DELETED = 0;
    const DELETED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_map_auth_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_group_id', 'request_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'auth_group_id' => Yii::t('common', 'Auth Group ID'),
            'request_id' => Yii::t('common', 'Request ID'),
        ];
    }

    public function getRequest()
    {
        return $this->hasOne(Request::className(), ['id' => 'request_id']);
    }

    public function getAuthGroup()
    {
        return $this->hasOne(AuthGroup::className(), ['id' => 'auth_group_id']);
    }

    public function getManagementUsers()
    {
        return $this->hasMany(ManagementUser::className(), ['auth_group_id' => 'auth_group_id']);
    }

    function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }
}
