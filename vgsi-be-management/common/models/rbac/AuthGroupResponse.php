<?php

namespace common\models\rbac;

use common\helpers\ErrorCode;
use common\models\ManagementUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AuthGroupResponse")
 * )
 */
class AuthGroupResponse extends AuthGroup
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="code", type="string"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="name_en", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="count_management_user", type="integer"),
     * @SWG\Property(property="type", type="integer"),
     * @SWG\Property(property="type_name", type="string"),
     * @SWG\Property(property="data_role", type="array",
     *      @SWG\Items(type="string", default="string"),
     *  ),
     * @SWG\Property(property="data_web", type="array",
     *      @SWG\Items(type="string", default="string"),
     *  ),
     * @SWG\Property(property="data_api", type="array",
     *      @SWG\Items(type="string", default="string"),
     *  ),
     */
    public function fields()
    {
        return [
            'id',
            'code',
            'name',
            'name_en',
            'description',
            'type',
            'type_name' => function($model){
                return AuthGroup::$type_list[$model->type];
            },
            'data_role' => function ($model) {
                return (!empty($model->data_role)) ? json_decode($model->data_role, true) : [];
            },
            'data_web' => function($model){
                $names = (!empty($model->data_role)) ? json_decode($model->data_role, true) : [];
                $authItems = AuthItem::find()->where(['name' => $names, 'type' => AuthItem::TYPE_ROLE])->all();
                $arrRes = [];
                foreach ($authItems as $authItem) {
                    $arrItems = (!empty($authItem->data_web)) ? json_decode($authItem->data_web, true) : [];
                    $arrRes   = array_merge($arrRes,$arrItems);
                    // $arrRes[$authItem->name]   = $arrItems;
                }
                $aryResult = array_unique($arrRes);
                return $aryResult;

            },
            'data_api' => function($model){
                $names = (!empty($model->data_role)) ? json_decode($model->data_role, true) : [];
                $authItems = AuthItemChild::find()->where(['parent' => $names])->all();
                $arrRes = [];
                foreach ($authItems as $authItem){
                    $arrRes[] =  $authItem->child;
                }
                $arrRes = array_unique($arrRes);
                return $arrRes;
            },
            'count_management_user' => function ($model) {
                $buildingCluster = Yii::$app->building->BuildingCluster;
                return (int)ManagementUser::find()->where(['is_deleted' => ManagementUser::NOT_DELETED, 'auth_group_id' => $model->id, 'building_cluster_id' => $buildingCluster->id])->count();
            }
        ];
    }
}
