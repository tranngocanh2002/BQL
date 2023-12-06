<?php

namespace backendQltt\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\base\Behavior;
use yii\behaviors\TimestampBehavior;
use common\helpers\MyDatetime;
use backendQltt\models\LoggerUser;
use yii\base\ActionFilter;
use yii\helpers\Json;
use yii\web\Request;
use common\models\User;

class LogBehavior extends Behavior
{
    public $actions = ['create', 'update', 'delete'];

    public $request = 'request';
    public $response = 'response';
    public $headers = 'headers';
    public $bodyParams = 'bodyParams';
    public $queryParams = 'queryParams';
    public $scope = 'default';
    const EVENT_AFTER_IMPORTFILE = 'afterImportFile';
    const EVENT_AFTER_EXPORTFILE = 'afterExportFile';
    const EVENT_AFTER_RESETPASSWORD = 'afterResetPassword';
    const EVENT_AFTER_LOGOUT = 'afterLogout';
    const EVENT_AFTER_INSERT = 'afterInsert';
    const EVENT_AFTER_DELETE = 'afterDelete';
    const EVENT_AFTER_UPDATE = 'afterEdit';

    // const EVENT_AFTER_LOGIN = 'afterLogin';
    public function init()
    {
        $this->request = Yii::$app->request;
        $this->response = Yii::$app->response;
        $this->bodyParams = Yii::$app->request->bodyParams;
        $this->queryParams = Yii::$app->request->queryParams;
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'logAfterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'logAfterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'logAfterDelete',
            self::EVENT_AFTER_IMPORTFILE    => 'logAfterImport',
            self::EVENT_AFTER_EXPORTFILE    => 'logAfterExport',
            self::EVENT_AFTER_RESETPASSWORD => 'afterResetPassword',
            self::EVENT_AFTER_LOGOUT        => 'afterLogout',
            self::EVENT_AFTER_INSERT        => 'afterInsert',
            self::EVENT_AFTER_UPDATE        => 'afterEdit',
            self::EVENT_AFTER_DELETE        => 'afterDelete',

            // self::EVENT_AFTER_LOGIN         => 'afterLogin',
        ];
    }


    public function afterInsert($event)
    {
        // Ghi log sau khi tạo mới một bản ghi
        // Lấy thông tin bản ghi sau khi tạo mới
        // $data = $this->owner->attributes;
        $request = $this->request;
        $response = $this->response;
        $object     = $this->checkObjectByController(Yii::$app->controller->id ?? "");
        $object_en  = $this->checkObjectEnByController(Yii::$app->controller->id ?? "");
        // Ghi log vào bảng logger_user
        $loggerUser = new LoggerUser();
        $loggerUser->building_cluster_id = Yii::$app->building->BuildingCluster ?? 1;
        $loggerUser->management_user_id  = Yii::$app->user->id ?? 0;
        $loggerUser->ip_address          = $request->getUserIP() ?? "";
        $loggerUser->user_agent          = $request->getUserAgent() ?? "";
        $loggerUser->scope               = $this->scope ?? "";
        $loggerUser->headers             = Json::encode($request->getHeaders()->toArray());
        $loggerUser->controller          = Yii::$app->controller->id ?? "";
        $loggerUser->request             = "request";
        $loggerUser->action              = "Thêm mới";
        $loggerUser->action_en           = "Add";
        $loggerUser->response            = "response";
        $loggerUser->body_params         = "body_params";
        $loggerUser->query_params        = "query_params";
        $loggerUser->authen              = "authen";
        $loggerUser->object              = $object;
        $loggerUser->object_en           = $object_en;
        $loggerUser->created_at          = time();
        $loggerUser->save();
    }

    public function afterEdit($event)
    {
        // Ghi log sau khi tạo mới một bản ghi
        // Lấy thông tin bản ghi sau khi tạo mới
        // $data = $this->owner->attributes;
        $request = $this->request;
        $response = $this->response;
        $object     = $this->checkObjectByController(Yii::$app->controller->id ?? "");
        $object_en  = $this->checkObjectEnByController(Yii::$app->controller->id ?? "");
        // Ghi log vào bảng logger_user
        $loggerUser = new LoggerUser();
        $loggerUser->building_cluster_id = Yii::$app->building->BuildingCluster ?? 1;
        $loggerUser->management_user_id  = Yii::$app->user->id ?? 0;
        $loggerUser->ip_address          = $request->getUserIP() ?? "";
        $loggerUser->user_agent          = $request->getUserAgent() ?? "";
        $loggerUser->scope               = $this->scope ?? "";
        $loggerUser->headers             = Json::encode($request->getHeaders()->toArray());
        $loggerUser->controller          = Yii::$app->controller->id ?? "";
        $loggerUser->request             = "request";
        $loggerUser->action              = "Cập nhật";
        $loggerUser->action_en           = "Edit";
        $loggerUser->response            = "response";
        $loggerUser->body_params         = "body_params";
        $loggerUser->query_params        = "query_params";
        $loggerUser->authen              = "authen";
        $loggerUser->object              = $object;
        $loggerUser->object_en           = $object_en;
        $loggerUser->created_at          = time();
        $loggerUser->save();
    }

    public function afterDelete($event)
    {
        // Ghi log sau khi tạo mới một bản ghi
        // Lấy thông tin bản ghi sau khi tạo mới
        // $data = $this->owner->attributes;
        $request = $this->request;
        $response = $this->response;
        $object     = $this->checkObjectByController(Yii::$app->controller->id ?? "");
        $object_en  = $this->checkObjectEnByController(Yii::$app->controller->id ?? "");
        // Ghi log vào bảng logger_user
        $loggerUser = new LoggerUser();
        $loggerUser->building_cluster_id = Yii::$app->building->BuildingCluster ?? 1;
        $loggerUser->management_user_id  = Yii::$app->user->id ?? 0;
        $loggerUser->ip_address          = $request->getUserIP() ?? "";
        $loggerUser->user_agent          = $request->getUserAgent() ?? "";
        $loggerUser->scope               = $this->scope ?? "";
        $loggerUser->headers             = Json::encode($request->getHeaders()->toArray());
        $loggerUser->controller          = Yii::$app->controller->id ?? "";
        $loggerUser->request             = "request";
        $loggerUser->action              = "Xóa";
        $loggerUser->action_en           = "Delete";
        $loggerUser->response            = "response";
        $loggerUser->body_params         = "body_params";
        $loggerUser->query_params        = "query_params";
        $loggerUser->authen              = "authen";
        $loggerUser->object              = $object;
        $loggerUser->object_en           = $object_en;
        $loggerUser->created_at          = time();
        $loggerUser->save();
    }

    public function logAfterInsert($event)
    {
        // Ghi log sau khi tạo mới một bản ghi
        // Lấy thông tin bản ghi sau khi tạo mới
        // $data = $this->owner->attributes;
        $request = $this->request;
        $response = $this->response;
        $object     = $this->checkObjectByController(Yii::$app->controller->id ?? "");
        $object_en  = $this->checkObjectEnByController(Yii::$app->controller->id ?? "");
        // Ghi log vào bảng logger_user
        $loggerUser = new LoggerUser();
        $loggerUser->building_cluster_id = Yii::$app->building->BuildingCluster ?? 1;
        $loggerUser->management_user_id  = Yii::$app->user->id ?? 0;
        $loggerUser->ip_address          = $request->getUserIP() ?? "";
        $loggerUser->user_agent          = $request->getUserAgent() ?? "";
        $loggerUser->scope               = $this->scope ?? "";
        $loggerUser->headers             = Json::encode($request->getHeaders()->toArray());
        $loggerUser->controller          = Yii::$app->controller->id ?? "";
        $loggerUser->request             = "request";
        $loggerUser->action              = "Thêm mới";
        $loggerUser->action_en           = "Create";
        $loggerUser->response            = "response";
        $loggerUser->body_params         = "body_params";
        $loggerUser->query_params        = "query_params";
        $loggerUser->authen              = "authen";
        $loggerUser->object              = $object;
        $loggerUser->object_en           = $object_en;
        $loggerUser->created_at          = time();
        $loggerUser->save();
    }


    public function logAfterUpdate($event)
    {
        // Ghi log sau khi cập nhật một bản ghi
        // Lấy thông tin bản ghi trước khi cập nhật
        // $oldData = $event->changedAttributes;
        // Lấy thông tin bản ghi sau khi cập nhật
        // $newData = $this->owner->attributes;
        $request = $this->request;
        $response = $this->response;
        if ("verify-otp" == Yii::$app->controller->action->id) {
            return;
        }
        // Ghi log vào bảng logger_user
        $object = $this->checkObjectByController(Yii::$app->controller->id ?? "");
        $object_en = $this->checkObjectEnByController(Yii::$app->controller->id ?? "");
        $actionss = "";
        $actionss_en = "";
        switch (Yii::$app->controller->action->id) {
            case "delete":
                $actionss = "Xóa";
                $actionss_en = "Delete";
                break;
                // case "profile" : $actionss = "Đổi mật khẩu"; break ;
            case "reset-password":
                $actionss = "Đặt lại mật khẩu";
                $object = "Xác thực";
                $actionss_en = "Reset password";
                $object_en = "Authentication";
                break;
            case "login":
                $actionss = "Đăng nhập";
                $object = "Xác thực";
                $actionss_en = "Login";
                $object_en = "Authentication";
                break;
            case "profile":
                $actionss = "Đổi mật khẩu";
                $object = "Tài khoản";
                $actionss_en = "Change password";
                $object_en = "Acount";
                break;
            case "inactive":
                $id = Yii::$app->getRequest()->getQueryParam('id');
                $userModel = User::findOne($id);
                if ($userModel->status == User::STATUS_INACTIVE) {
                    $actionss = "Dừng kích hoạt";
                    $actionss_en = "Inactive";
                } else if ($userModel->status == User::STATUS_ACTIVE) {
                    $actionss = "Kích hoạt";
                    $actionss_en = "Active";
                } else {
                    $actionss = "Dừng kích hoạt";
                    $actionss_en = "Inactive";
                }
                break;

            default:
                $actionss = "Chỉnh sửa";
                $actionss_en = "Edit";
                break;
        }
        if ("reset-password" == Yii::$app->controller->action->id && "user" == Yii::$app->controller->id) {
            $actionss = "Đặt lại mật khẩu";
            $actionss_en = "Reset password";
            $object   = "Người dùng";
            $object_en   = "User";
        }
        if ("profile" == Yii::$app->controller->action->id && "user" == Yii::$app->controller->id) {
            $actionss = "Chỉnh sửa";
            $actionss_en = "Edit";
            $object   = "Tài khoản";
            $object_en   = "Account";
        }
        // $actionss = Yii::$app->controller->action->id;
        // $object = Yii::$app->controller->id;
        $loggerUser = new LoggerUser();
        $loggerUser->building_cluster_id = Yii::$app->building->BuildingCluster ?? 1;
        $loggerUser->management_user_id  = Yii::$app->user->id ?? 0;
        $loggerUser->ip_address          = $request->getUserIP() ?? "";
        $loggerUser->user_agent          = $request->getUserAgent() ?? "";
        $loggerUser->scope               = $this->scope ?? "";
        $loggerUser->headers             = Json::encode($request->getHeaders()->toArray());
        $loggerUser->controller          = Yii::$app->controller->id ?? "";
        $loggerUser->request             = "request";
        $loggerUser->action              = $actionss;
        $loggerUser->action_en           = $actionss_en;
        $loggerUser->response            = "response";
        $loggerUser->body_params         = "body_params";
        $loggerUser->query_params        = "query_params";
        $loggerUser->authen              = "authen";
        $loggerUser->object              = $object;
        $loggerUser->object_en           = $object_en;
        $loggerUser->created_at          = time();
        $loggerUser->save();
    }

    public function logAfterDelete($event)
    {
        // Ghi log sau khi xóa một bản ghi
        // Lấy thông tin bản ghi trước khi xóa
        // $data = $event->oldAttributes;
        $request = $this->request;
        $response = $this->response;
        $object = $this->checkObjectByController(Yii::$app->controller->id ?? "");
        $object_en = $this->checkObjectEnByController(Yii::$app->controller->id ?? "");
        // Ghi log vào bảng logger_user
        $loggerUser = new LoggerUser();
        $loggerUser->building_cluster_id = Yii::$app->building->BuildingCluster ?? 1;
        $loggerUser->management_user_id  = Yii::$app->user->id ?? 0;
        $loggerUser->ip_address          = $request->getUserIP() ?? "";
        $loggerUser->user_agent          = $request->getUserAgent() ?? "";
        $loggerUser->scope               = $this->scope ?? "";
        $loggerUser->headers             = Json::encode($request->getHeaders()->toArray());
        $loggerUser->controller          = Yii::$app->controller->id ?? "";
        $loggerUser->request             = "request";
        $loggerUser->action              = "Xóa";
        $loggerUser->action_en           = "Delete";
        $loggerUser->response            = "response";
        $loggerUser->body_params         = "body_params";
        $loggerUser->query_params        = "query_params";
        $loggerUser->authen              = "authen";
        $loggerUser->object              = $object;
        $loggerUser->object_en           = $object_en;
        $loggerUser->created_at          = time();
        $loggerUser->save();
    }
    public function logAfterImport($event)
    {
        // Ghi log sau khi xóa một bản ghi
        // Lấy thông tin bản ghi trước khi xóa
        // $data = $event->oldAttributes;
        $request = $this->request;
        $response = $this->response;
        $object = $this->checkObjectByController(Yii::$app->controller->id ?? "");
        $object_en = $this->checkObjectEnByController(Yii::$app->controller->id ?? "");
        // Ghi log vào bảng logger_user
        $loggerUser = new LoggerUser();
        $loggerUser->building_cluster_id = Yii::$app->building->BuildingCluster ?? 1;
        $loggerUser->management_user_id  = Yii::$app->user->id ?? 0;
        $loggerUser->ip_address          = $request->getUserIP() ?? "";
        $loggerUser->user_agent          = $request->getUserAgent() ?? "";
        $loggerUser->scope               = $this->scope ?? "";
        $loggerUser->headers             = Json::encode($request->getHeaders()->toArray());
        $loggerUser->controller          = Yii::$app->controller->id ?? "";
        $loggerUser->request             = "request";
        $loggerUser->action              = "Tải lên danh sách";
        $loggerUser->action_en           = "Import data";
        $loggerUser->response            = "response";
        $loggerUser->body_params         = "body_params";
        $loggerUser->query_params        = "query_params";
        $loggerUser->authen              = "authen";
        $loggerUser->object              = $object;
        $loggerUser->object_en           = $object_en;
        $loggerUser->created_at          = time();
        $loggerUser->save();
    }
    public function logAfterExport($event)
    {
        // Ghi log sau khi xóa một bản ghi
        // Lấy thông tin bản ghi trước khi xóa
        // $data = $event->oldAttributes;
        $request = $this->request;
        $response = $this->response;
        $object = $this->checkObjectByController(Yii::$app->controller->id ?? "");
        $object_en = $this->checkObjectEnByController(Yii::$app->controller->id ?? "");
        // Ghi log vào bảng logger_user
        $loggerUser = new LoggerUser();
        $loggerUser->building_cluster_id = Yii::$app->building->BuildingCluster ?? 1;
        $loggerUser->management_user_id  = Yii::$app->user->id ?? 0;
        $loggerUser->ip_address          = $request->getUserIP() ?? "";
        $loggerUser->user_agent          = $request->getUserAgent() ?? "";
        $loggerUser->scope               = $this->scope ?? "";
        $loggerUser->headers             = Json::encode($request->getHeaders()->toArray());
        $loggerUser->controller          = Yii::$app->controller->id ?? "";
        $loggerUser->request             = "request";
        $loggerUser->action              = "Tải xuống danh sách";
        $loggerUser->action_en           = "Export data";
        $loggerUser->response            = "response";
        $loggerUser->body_params         = "body_params";
        $loggerUser->query_params        = "query_params";
        $loggerUser->authen              = "authen";
        $loggerUser->object              = $object;
        $loggerUser->object_en           = $object_en;
        $loggerUser->created_at          = time();
        $loggerUser->save();
    }

    public function afterResetPassword($event)
    {
        // Ghi log sau khi cập nhật một bản ghi
        // Lấy thông tin bản ghi trước khi cập nhật
        // $oldData = $event->changedAttributes;
        // Lấy thông tin bản ghi sau khi cập nhật
        // $newData = $this->owner->attributes;
        $request = $this->request;
        $response = $this->response;
        // Ghi log vào bảng logger_user
        $object = $this->checkObjectByController(Yii::$app->controller->id ?? "");
        $actionss = "";
        switch (Yii::$app->controller->action->id) {
            case "delete":
                $actionss = "Xóa";
                break;
            case "inactive":
                $id = Yii::$app->getRequest()->getQueryParam('id');
                $userModel = User::findOne($id);
                if ($userModel->status == User::STATUS_INACTIVE) {
                    $actionss = "Kích hoạt";
                } else if ($userModel->status == User::STATUS_ACTIVE) {
                    $actionss = "Dừng kích hoạt";
                } else {
                    $actionss = "Dừng kích hoạt";
                }
                break;
            default:
                $actionss = "Chỉnh sửa";
                break;
        }
        $loggerUser = new LoggerUser();
        $loggerUser->building_cluster_id = Yii::$app->building->BuildingCluster ?? 1;
        $loggerUser->management_user_id  = Yii::$app->user->id ?? 0;
        $loggerUser->ip_address          = $request->getUserIP() ?? "";
        $loggerUser->user_agent          = $request->getUserAgent() ?? "";
        $loggerUser->scope               = $this->scope ?? "";
        $loggerUser->headers             = Json::encode($request->getHeaders()->toArray());
        $loggerUser->controller          = Yii::$app->controller->id ?? "";
        $loggerUser->request             = "request";
        $loggerUser->action              = "Đặt lại mật khẩu";
        $loggerUser->action_en           = "Reset password";
        $loggerUser->response            = "response";
        $loggerUser->body_params         = "body_params";
        $loggerUser->query_params        = "query_params";
        $loggerUser->authen              = "authen";
        $loggerUser->object              = "Xác thực";
        $loggerUser->object_en           = "Authentication";
        $loggerUser->created_at          = time();
        $loggerUser->save();
    }

    public function afterLogout($event)
    {
        // Ghi log sau khi cập nhật một bản ghi
        // Lấy thông tin bản ghi trước khi cập nhật
        // $oldData = $event->changedAttributes;
        // Lấy thông tin bản ghi sau khi cập nhật
        // $newData = $this->owner->attributes;
        $request = $this->request;
        $response = $this->response;
        // Ghi log vào bảng logger_user
        $object = $this->checkObjectByController(Yii::$app->controller->id ?? "");
        $object_en = $this->checkObjectEnByController(Yii::$app->controller->id ?? "");
        $loggerUser = new LoggerUser();
        $loggerUser->building_cluster_id = Yii::$app->building->BuildingCluster ?? 1;
        $loggerUser->management_user_id  = Yii::$app->user->id ?? 0;
        $loggerUser->ip_address          = $request->getUserIP() ?? "";
        $loggerUser->user_agent          = $request->getUserAgent() ?? "";
        $loggerUser->scope               = $this->scope ?? "";
        $loggerUser->headers             = Json::encode($request->getHeaders()->toArray());
        $loggerUser->controller          = Yii::$app->controller->id ?? "";
        $loggerUser->request             = "request";
        $loggerUser->action              = "Đăng xuất";
        $loggerUser->action_en           = "Logout";
        $loggerUser->response            = "response";
        $loggerUser->body_params         = "body_params";
        $loggerUser->query_params        = "query_params";
        $loggerUser->authen              = "authen";
        $loggerUser->object              = "Xác thực";
        $loggerUser->object_en           = "Authentication";
        $loggerUser->created_at          = time();
        $loggerUser->save();
    }
    // public function afterLogin($event)
    // {
    //     // Ghi log sau khi cập nhật một bản ghi
    //     // Lấy thông tin bản ghi trước khi cập nhật
    //     // $oldData = $event->changedAttributes;
    //     // Lấy thông tin bản ghi sau khi cập nhật
    //     // $newData = $this->owner->attributes;
    //     $request = $this->request;
    //     $response = $this->response;
    //     // Ghi log vào bảng logger_user
    //     $object = $this->checkObjectByController(Yii::$app->controller->id ?? "");
    //     $loggerUser = new LoggerUser();
    //     $loggerUser->building_cluster_id = Yii::$app->building->BuildingCluster ?? 1 ;
    //     $loggerUser->management_user_id  = Yii::$app->user->id ?? 0;
    //     $loggerUser->ip_address          = $request->getUserIP() ?? "";
    //     $loggerUser->user_agent          = $request->getUserAgent() ?? "";
    //     $loggerUser->scope               = $this->scope ?? "";
    //     $loggerUser->headers             = Json::encode($request->getHeaders()->toArray());
    //     $loggerUser->controller          = Yii::$app->controller->id ?? "";
    //     $loggerUser->request             = "request";
    //     $loggerUser->action              = "Đăng nhập";
    //     $loggerUser->response            = "response";
    //     $loggerUser->body_params         = "body_params";
    //     $loggerUser->query_params        = "query_params";
    //     $loggerUser->authen              = "authen";
    //     $loggerUser->object              = "Xác thực";
    //     $loggerUser->created_at          = time();
    //     $loggerUser->save();
    // }
    public function checkObjectByController($controller = "")
    {
        $object = "";
        switch ($controller) {
            case "building-cluster":
                $object = "Dự án";
                break;
            case "announcement-campaign":
                $object = "Tin tức";
                break;
            case "user":
                $object = "Người dùng";
                break;
            case "announcement-template":
                $object = "Mẫu tin tức";
                break;
            case "user-role":
                $object = "Nhóm quyền";
                break;
            case "site":
                $object = "Tài khoản";
                break;
            default:
                $object = "Người dùng";
                break;
        }
        return $object;
    }
    public function checkObjectEnByController($controller = "")
    {
        $object = "";
        switch ($controller) {
            case "building-cluster":
                $object = "Project";
                break;
            case "announcement-campaign":
                $object = "News";
                break;
            case "user":
                $object = "User manager";
                break;
            case "announcement-template":
                $object = "News templates";
                break;
            case "user-role":
                $object = "Authorization group";
                break;
            case "site":
                $object = "Account";
                break;
            default:
                $object = "User";
                break;
        }
        return $object;
    }
}
