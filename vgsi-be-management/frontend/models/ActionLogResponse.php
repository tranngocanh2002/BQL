<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ActionLog;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ActionLogResponse")
 * )
 */
class ActionLogResponse extends ActionLog
{
    /**
     * @SWG\Property(property="id", type="string"),
     * @SWG\Property(property="building_cluster_id", type="integer"),
     * @SWG\Property(property="management_user_id", type="integer"),
     * @SWG\Property(property="management_user_name", type="string"),
     * @SWG\Property(property="ip_address", type="string"),
     * @SWG\Property(property="controller", type="string"),
     * @SWG\Property(property="controller_name", type="string"),
     * @SWG\Property(property="action", type="string"),
     * @SWG\Property(property="action_name", type="string"),
     * @SWG\Property(property="body_params", type="object"),
     * @SWG\Property(property="query_params", type="object"),
     * @SWG\Property(property="created_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id' => function ($model) {
                return $model->_id;
            },
            'building_cluster_id',
            'management_user_id',
            'management_user_name' => function ($model) {
                if ($model->managementUser) {
                    return $model->managementUser->first_name . ' ' . $model->managementUser->last_name;
                }
                return '';
            },
            'ip_address',
            'controller',
            'controller_name' => function($model){
                return Yii::t('action-log', $model->controller);
//                return Yii::t('action', str_replace('-', ' ', $model->controller));
            },
            'action',
            'action_name' => function($model){
                return Yii::t('action-log', $model->action);
//                return Yii::t('action', str_replace('-', ' ', $model->action));
            },
            'body_params',
            'query_params',
            'created_at',
        ];
    }
}
