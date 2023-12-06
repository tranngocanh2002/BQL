<?php

namespace common\models\rbac;

use common\helpers\ErrorCode;
use common\models\ManagementUser;
use common\models\RequestMapAuthGroup;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AuthGroupCreateForm")
 * )
 */
class AuthGroupCreateForm extends Model
{
    /**
     * @SWG\Property(description="Id", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Type : 0 - Ban quản lý, 1 - Ban quản trị", default=0, type="integer")
     * @var integer
     */
    public $type;


    /**
     * @SWG\Property(description="Name", default="", type="string")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="Name En", default="", type="string")
     * @var string
     */
    public $name_en;

    /**
     * @SWG\Property(description="Description", default="", type="string")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(property="data_role", type="array", description="role list",
     *      @SWG\Items(type="string", default="string"),
     * ),
     * @var array
     */
    public $data_role;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'name_en', 'description'], 'string'],
            [['data_role'], 'safe'],
            [['id'], 'required', "on" => ['update', 'delete']],
            [['id', 'type'], 'integer'],
        ];
    }

    public function create(){
        $buildingCluster = Yii::$app->building->BuildingCluster;
        if(empty($buildingCluster)){
            return [
                'success' => false,
                'message' => Yii::t('common', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $item = new AuthGroup();
        $item->load((array)$this->attributes,'');
        if($item->type == null){ $item->type = AuthGroup::TYPE_BQL;}
        $item->code = 'CODE'.time();
        $item->data_role = json_encode($item->data_role);
        $item->building_cluster_id = $buildingCluster->id;
        if(!$item->save()){
            return [
                'success' => false,
                'message' => Yii::t('common', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $item->getErrors()
            ];
        }else{
            return [
                'success' => true,
            ];
        }
    }

    public function update(){
        $buildingCluster = Yii::$app->building->BuildingCluster;
        if(empty($buildingCluster)){
            return [
                'success' => false,
                'message' => Yii::t('common', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $item = AuthGroup::findOne(['id' => (int)$this->id, 'building_cluster_id' => $buildingCluster->id]);
        if($item){
            $item->load((array)$this->attributes,'');
            if($item->type == null){ $item->type = AuthGroup::TYPE_BQL;}
            $item->data_role = json_encode($item->data_role);
            if($item->update() === false){
                return [
                    'success' => false,
                    'message' => Yii::t('common', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }else{
                $item->updatePermissionUser();
                return [
                    'success' => true,
                    'message' => Yii::t('common', "Update success"),
                ];
            }
        }else{
            return [
                'success' => false,
                'message' => Yii::t('common', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
    public function delete(){
        $buildingCluster = Yii::$app->building->BuildingCluster;
        if(empty($buildingCluster)){
            return [
                'success' => false,
                'message' => Yii::t('common', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $item = AuthGroup::findOne(['id' => (int)$this->id, 'building_cluster_id' => $buildingCluster->id]);
        if(!empty($item)){
            $managementUser = ManagementUser::findOne(['is_deleted' => ManagementUser::NOT_DELETED, 'auth_group_id' => $item->id, 'building_cluster_id' => $buildingCluster->id]);
            $requestMapAuthGroup = RequestMapAuthGroup::findOne(['auth_group_id' => $item->id]);
            if(!empty($managementUser) || !empty($requestMapAuthGroup)){
                return [
                    'success' => false,
                    'message' => Yii::t('common', "Auth Group is being used"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            if(!$item->delete()){
                return [
                    'success' => false,
                    'message' => Yii::t('common', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            return [
                'success' => true,
                'message' => Yii::t('common', "Delete Success"),
            ];
        }else{
            return [
                'success' => false,
                'message' => Yii::t('common', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
    }
}
