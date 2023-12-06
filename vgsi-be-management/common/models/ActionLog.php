<?php

namespace common\models;

use Yii;

/**
 * This is the model class for collection "action_log".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property integer $building_cluster_id
 * @property integer $management_user_id
 * @property string $ip_address
 * @property string $user_agent
 * @property string $scope
 * @property string $headers
 * @property string $controller
 * @property string $action
 * @property string $request
 * @property string $response
 * @property string $body_params
 * @property string $query_params
 * @property integer $authen
 * @property integer $created_at
 *
 * @property BuildingCluster $buildingCluster
 * @property ManagementUser $managementUser
 */
class ActionLog extends \yii\mongodb\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'action_log_'.date('mY', time());
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'building_cluster_id',
            'management_user_id',
            'ip_address',
            'user_agent',
            'scope',
            'headers',
            'request',
            'response',
            'body_params',
            'query_params',
            'controller',
            'action',
            'authen',
            'created_at',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'management_user_id', 'ip_address', 'user_agent', 'scope', 'headers', 'request', 'response', 'body_params', 'query_params', 'controller', 'action','authen', 'created_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'building_cluster_id' => 'Building Cluster Id',
            'management_user_id' => 'Management User Id',
            'ip_address' => 'Ip Address',
            'user_agent' => 'User Agent',
            'scope' => 'Scope',
            'headers' => 'Headers',
            'request' => 'Request',
            'response' => 'Response',
            'body_params' => 'Body Params',
            'query_params' => 'Query Params',
            'controller' => 'Controller',
            'action' => 'Action',
            'authen' => 'Authen',
            'created_at' => 'Created At',
        ];
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
        return $this->hasOne(ManagementUser::className(), ['id' => 'management_user_id']);
    }
}
