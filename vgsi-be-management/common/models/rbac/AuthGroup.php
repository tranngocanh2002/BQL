<?php

namespace common\models\rbac;

use common\models\BuildingCluster;
use common\models\ManagementUser;
use common\models\ManagementUserAccessToken;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "auth_group".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $name_en
 * @property string $description
 * @property string $data_role
 * @property int $type
 * @property int $building_cluster_id
 * @property int $building_area_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property BuildingCluster $buildingCluster
 */
class AuthGroup extends \yii\db\ActiveRecord
{
    const TYPE_BQL = 0;
    const TYPE_BQT = 1;

    public static $type_list = [
        self::TYPE_BQL => "Ban Quản Lý",
        self::TYPE_BQT => "Ban Quản Trị"
    ];

    public static $type_list_en = [
        self::TYPE_BQL => "Management Board",
        self::TYPE_BQT => "Board of Directors"
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'data_role'], 'string'],
            [['type', 'building_area_id', 'building_cluster_id', 'created_at', 'updated_at'], 'integer'],
            [['code', 'name', 'name_en'], 'string', 'max' => 64],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'code' => Yii::t('common', 'Code'),
            'name' => Yii::t('common', 'Name'),
            'name_en' => Yii::t('common', 'Name En'),
            'description' => Yii::t('common', 'Description'),
            'data_role' => Yii::t('common', 'Data Role'),
            'type' => Yii::t('common', 'Type'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster Id'),
            'building_area_id' => Yii::t('common', 'Building Area Id'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' =>  Yii::t('common', 'Updated At'),
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

    public function getDataRoleArray()
    {
        return (!empty($this->data_role)) ? json_decode($this->data_role, true) : [];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuildingCluster()
    {
        return $this->hasOne(BuildingCluster::className(), ['id' => 'building_cluster_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagementUser()
    {
        return $this->hasMany(ManagementUser::className(), ['auth_group_id' => 'id']);
    }

    public function updatePermissionUser()
    {
        $dataRoleArray = $this->getDataRoleArray();
        $managementUsers = ManagementUser::findAll(['auth_group_id' => $this->id, 'is_deleted' => ManagementUser::NOT_DELETED]);
        $authItems = AuthItem::findAll(['name' => $dataRoleArray]);
        foreach ($managementUsers as $managementUser) {
            //xóa quyền cũ
            AuthAssignment::deleteAll(['user_id' => $managementUser->id]);
            //add quyền mới
            $insert_array = [];
            foreach ($authItems as $role) {
                $insert_array[] = [
                    (string)$managementUser->id,
                    $role->name,
                    time()
                ];
            }
            Yii::$app->db->createCommand()->batchInsert('auth_assignment', [
                'user_id', 'item_name', 'created_at'
            ], $insert_array)->execute();

            //logout ManagementUser liên quan với tập quyền
            ManagementUserAccessToken::deleteAll(['management_user_id' => $managementUser->id, 'type' => ManagementUserAccessToken::TYPE_ACCESS_TOKEN]);
        }
    }

    /**
     * Find board auth group by buidling cluster id
     *
     * @param $buildingClusterId
     * @return mixed
     */
    public static function findBoardByBuildingClusterId($buildingClusterId)
    {
        return AuthGroup::find(['building_cluster_id' => $buildingClusterId])->orderBy(['id' => SORT_DESC])->one();
    }
}
