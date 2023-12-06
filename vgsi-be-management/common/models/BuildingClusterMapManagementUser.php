<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "building_cluster_map_management_user".
 *
 * @property int $id
 * @property int $building_cluster_id
 * @property int $management_user_id
 */
class BuildingClusterMapManagementUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'building_cluster_map_management_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'management_user_id'], 'integer'],
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
            'management_user_id' => Yii::t('common', 'Management User ID'),
        ];
    }
}
