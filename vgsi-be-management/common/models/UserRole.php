<?php

namespace common\models;

use Yii;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use backendQltt\models\LoggerUser;
use backendQltt\models\LogBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_role}}".
 *
 * @property string $id
 * @property string $name
 * @property string $permission
 *
 * @property User[] $users
 */
class UserRole extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user_role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'permission'], 'required'],
            [['permission'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            // TimestampBehavior::className(),
            // LogBehavior::className(),
            'log' => [
                'class' => LogBehavior::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('common', 'ID'),
            'name' => Yii::t('common', 'Role Name'),
            'permission' => Yii::t('common', 'Permission'),
            'description' => Yii::t('common', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        return $this->hasMany(User::className(), ['role_id' => 'id']);
    }

    public function genPemission($permission = [])
    {
        if ($this->id === 1) {
            $permission = array_merge($permission, [
                'user-role' => [
                    'index',
                    'create',
                    'update',
                    'delete',
                    // 'view',
                ]
            ]);
        }

        $permission['user'][] = 'profile';
        $permission['user'][] = 'export-template';
        $permission['building-cluster'][] = 'to-bql';

        return array_merge($permission, [
            "upload" => [
                "tmp"
            ],
        ]);
    }
}
