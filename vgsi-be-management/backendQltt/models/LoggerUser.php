<?php

namespace backendQltt\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\helpers\MyDatetime;
use common\models\BuildingCluster;
use common\models\ManagementUser;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * This is the model class for table "logger_user".
 * 
 * @property int $id
 * @property int $building_cluster_id
 * @property int $management_user_id
 * @property string $ip_address
 * @property string $user_agent
 * @property string $scope
 * @property string $action
 * @property string $action_en
 * @property string $controller
 * @property string $request
 * @property string $response
 * @property string $body_params
 * @property string $query_params
 * @property string $authen
 * @property int $created_at
 * @property string $object
 * @property string $object_en
 * @property BuildingCluster $buildingCluster
 * @property ManagementUser $managementUser
 * @property User $user
 */
class LoggerUser extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'logger_user';
    }

    public function rules()
    {
        return [];
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'management_user_id']);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = LoggerUser::find()->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 1000,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC,
                ]
            ],
        ]);
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        if (0 >= count($params)) {
            return $dataProvider;
        }
        if (isset($params['LoggerUser'])) {
            $object = $params['LoggerUser']['object'] ?? "";
            $action = $params['LoggerUser']['action'] ?? "";
            // $created_at = strtotime($params['LoggerUser']['created_at']) ?? "";
            // $request    = strtotime($params['LoggerUser']['request']) ?? "";
            // $query->andFilterWhere(['like', 'object', $object]);
            $fromdate = strtotime($params['LoggerUser']['created_at']) ?? "";
            $todate = strtotime($params['LoggerUser']['request']) ?? "";
        }

        if (!empty($object)) {
            $query->andFilterWhere(['like', 'object', trim($object)]);
        }

        if (!empty($action)) {
            $query->andFilterWhere(['like', 'action', trim($action)]);
        }

        if (!empty($fromdate) && empty($todate)) {
            $query->andFilterWhere(['>=', 'created_at', trim($fromdate)]);
        }
        if (!empty($todate) && empty($fromdate)) {
            $query->andFilterWhere(['<=', 'created_at', trim($todate)]);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $query->andFilterWhere(['>=', 'created_at', trim($fromdate)]);
            $query->andFilterWhere(['<=', 'created_at', trim($todate)]);
        }
        $management_user_id = $params['LoggerUser']['management_user_id'] ?? "";
        if (!empty($management_user_id)) {
            // $managementUserNames = ManagementUser::find(['like','first_name',$management_user_id])->all();
            // $aryId = [];
            // foreach($managementUserNames as $managementUserName)
            // {
            //     $aryId[] = (int)$managementUserName->id;
            // }
            // var_dump($aryId);die();
            // $query->andWhere(['in', 'management_user_id', $aryId]);
            $query->innerJoinWith('user')
                ->andWhere(['like', 'user.full_name', $management_user_id]);
        }

        return $dataProvider;
    }
}