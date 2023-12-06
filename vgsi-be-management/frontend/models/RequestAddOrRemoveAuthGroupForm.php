<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\RequestCategory;
use common\models\RequestMapAuthGroup;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="RequestAddOrRemoveAuthGroupForm")
 * )
 */
class RequestAddOrRemoveAuthGroupForm extends Model
{
    const TYPE_ADD = 1;
    /**
     * @SWG\Property(description="Request Id", default=1, type="integer")
     * @var integer
     */
    public $request_id;

    /**
     * @SWG\Property(description="Type - 1 : thêm vào, 0 : loại bỏ", default=1, type="integer")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(property="auth_group_ids", type="array",
     *     @SWG\Items(type="integer", default=1),
     * ),
     * @var array
     */
    public $auth_group_ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'type', 'auth_group_ids'], 'required'],
            [['request_id', 'type'], 'integer'],
            [['auth_group_ids'], 'safe'],
        ];
    }

    public function addOrRemove()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            //check quyen xem chi tiet
            $requestMapAuthGroup = RequestMapAuthGroup::findOne(['request_id' => (int)$this->request_id, 'auth_group_id' => $user->auth_group_id]);
            if(empty($requestMapAuthGroup)){
                return [
                    'success' => true,
                    'message' => Yii::t('frontend', "Bạn không có quyền truy cập chức năng này"),
                    'statusCode' => ErrorCode::ERROR_PERMISSION_DENIED,
                ];
            }
            if(empty($this->auth_group_ids) || !is_array($this->auth_group_ids)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            if($this->type == self::TYPE_ADD){
                foreach ($this->auth_group_ids as $auth_group_id){
                    $check = RequestMapAuthGroup::findOne(['request_id' => $this->request_id, 'auth_group_id' => (int)$auth_group_id]);
                    if(!empty($check)){ continue; }
                    $requestMapAuthGroup = new RequestMapAuthGroup();
                    $requestMapAuthGroup->request_id = $this->request_id;
                    $requestMapAuthGroup->auth_group_id = (int)$auth_group_id;
                    if(!$requestMapAuthGroup->save()){
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                        ];
                    }
                }
            }else{
                RequestMapAuthGroup::deleteAll(['auth_group_id' => $this->auth_group_ids, 'request_id' => $this->request_id]);
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Update success"),
            ];
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
