<?php

namespace common\models\rbac;

use common\helpers\ErrorCode;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AuthItemUpdateForm")
 * )
 */
class AuthItemUpdateForm extends Model
{
    /**
     * @SWG\Property(description="Name", default="", type="string")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="description", default="", type="string")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(type="array", description="Permissions",
     *      @SWG\Items(type="string", default="string"),
     * ),
     * @var array
     */
    public $permissions;

    /**
     * @SWG\Property(type="array", description="role web",
     *      @SWG\Items(type="string", default="string"),
     * ),
     * @var array
     */
    public $data_web;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['permissions', 'data_web', 'description'], 'safe']
        ];
    }

    public function create(){
        $auth = \Yii::$app->authManager;
        $role = $auth->getRole($this->name);
        if(!$role){
            $role = $auth->createRole($this->name);
            $role->description = $this->description;
            $auth->add($role);
        }else{
            return [
                'success' => false,
                'message' => Yii::t('common', "Role exits"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        if(!empty($this->permissions)){
            foreach ($this->permissions as $permission){
                $auth->addChild($role, $auth->getPermission($permission));
            }
        }

        $item = AuthItem::findOne(['name' => $this->name]);
        if($item){
            if(!empty($this->data_web)){
                $item->data_web = json_encode($this->data_web);
            }
            if($item->update() === false){
                return [
                    'success' => false,
                    'message' => Yii::t('common', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }else{
                return '';
            }
        }else{
            return [
                'success' => false,
                'message' => Yii::t('common', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function update(){
        $item = AuthItem::findOne(['name' => $this->name]);
        if($item){
            if(!empty($this->data_web)){
                $item->data_web = json_encode($this->data_web);
            }
            $item->description = $this->description;
            if(!empty($this->permissions)){
                $auth = Yii::$app->authManager;
                $role = $auth->getRole($this->name);
                $auth->removeChildren($role);
                foreach ($this->permissions as $permission){
                    $auth->addChild($role, $auth->getPermission($permission));
                }
            }
            if($item->update() === false){
                return [
                    'success' => false,
                    'message' => Yii::t('common', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }else{
                return '';
            }
        }else{
            return [
                'success' => false,
                'message' => Yii::t('common', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
