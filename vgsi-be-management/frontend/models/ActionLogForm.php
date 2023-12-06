<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ActionLog;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

class ActionLogForm extends Model
{
    public $scope;
    public $controller;
    public $action;
    public $user_id ; 

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scope', 'controller', 'action'], 'safe'],
        ];
    }

    public function create()
    {
        if(empty($this->scope)){
            $this->scope = 'API_MANAGEMENT';
        }
        if(empty($this->controller)){
            $this->controller = Yii::$app->controller->id;
        }
        if(empty($this->action)){
            $this->action = Yii::$app->controller->action->id;
        }

        $buildingCluster = Yii::$app->building->BuildingCluster;
        $authen  = (Yii::$app->user->getIsGuest())?false:true;
        $request = Yii::$app->request;
        $response = Yii::$app->response;

        $action_log = new ActionLog();
        if($buildingCluster){
            $action_log->building_cluster_id = $buildingCluster->id;
        }
        if($authen){
            $action_log->management_user_id = Yii::$app->user->id;
        }
        if($this->action == "login"){
            $action_log->management_user_id = $this->user_id ; 
            $authen = true ; 
        }
        $action_log->ip_address = $request->getUserIP();
        $action_log->user_agent = $request->getUserAgent();
        $action_log->scope = $this->scope;
        $action_log->headers = Json::encode($request->getHeaders()->toArray());
        $action_log->request = $request;
        $action_log->body_params = Yii::$app->request->bodyParams;
        $action_log->query_params = Yii::$app->request->queryParams;
        $action_log->response = $response;
        $action_log->controller = $this->controller;
        $action_log->action = $this->action;
        $action_log->authen = $authen;
        $action_log->created_at = time();
        if(!$action_log->save()){
            Yii::error($action_log->errors);
        };
    }
}
