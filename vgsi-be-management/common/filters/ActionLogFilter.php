<?php

namespace common\filters;

use common\models\ActionLog;
use Yii;
use yii\base\ActionFilter;
use yii\helpers\Json;
use yii\web\Request;

/**
 * Created by PhpStorm.
 * User: nguyennhuluc1990@gmail.com
 * Date: 10/10/2019
 * Time: 2:26 CH
 */
class ActionLogFilter extends ActionFilter
{
    /**
     * @var $request Request
     */
    public $request = 'request';
    public $response = 'response';
    public $headers = 'headers';
    public $bodyParams = 'bodyParams';
    public $queryParams = 'queryParams';

    public $scope = 'default';


    /**
     * Initializes the [[rules]] array by instantiating rule objects from configurations.
     */
    public function init()
    {
        parent::init();
        $this->request = Yii::$app->request;
        $this->response = Yii::$app->response;
        $this->bodyParams = Yii::$app->request->bodyParams;
        $this->queryParams = Yii::$app->request->queryParams;
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        if ($action->id != "login") {
            $is_check_inject = true;
            $inJectModels = [
                'callback' => [
                    'sms',
                    'email',
                    'notify',
                ]
            ];
            if ($is_check_inject == true && isset($inJectModels[$action->controller->id])) {
                if (in_array($action->id, $inJectModels[$action->controller->id])) {
                    return parent::beforeAction($action);
                }
            }

            $authen  = (Yii::$app->user->getIsGuest()) ? false : true;
            $request = $this->request;
            $response = $this->response;

            $buildingCluster = Yii::$app->building->BuildingCluster;

            $action_log = new ActionLog();
            if ($buildingCluster) {
                $action_log->building_cluster_id = $buildingCluster->id;
            }
            if ($authen) {
                $action_log->management_user_id = Yii::$app->user->id;
            }
            $action_log->ip_address = $request->getUserIP();
            $action_log->user_agent = $request->getUserAgent();
            $action_log->scope = $this->scope;
            $action_log->headers = Json::encode($request->getHeaders()->toArray());
            $action_log->request = $request;
            $action_log->body_params = $this->bodyParams;
            $action_log->query_params = $this->queryParams;
            $action_log->response = $response;
            $action_log->controller = $action->controller->id;
            $action_log->action = $action->id;
            $action_log->authen = $authen;
            $action_log->created_at = time();
            $action_log->save();
        }
        return parent::beforeAction($action);

    }
}
