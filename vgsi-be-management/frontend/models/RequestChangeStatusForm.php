<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Request;
use common\models\RequestMapAuthGroup;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="RequestChangeStatusForm")
 * )
 */
class RequestChangeStatusForm extends Model
{
    /**
     * @SWG\Property(description="Request Id", default=1, type="integer")
     * @var integer
     */
    public $request_id;

    /**
     * @SWG\Property(description="Status", default=1, type="integer")
     * @var integer
     */
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'status'], 'required'],
            [['request_id', 'status'], 'integer'],
            [['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => Request::className(), 'targetAttribute' => ['request_id' => 'id']],
        ];
    }

    public function changeStatus()
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
            $request = Request::findOne(['id' => $this->request_id]);
            if($this->status == Request::STATUS_CLOSE){
                $request->type_close = Request::TYPE_BQL_CLOSE;
            }
            $request->status = $this->status;
            if(!$request->save()){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "System busy"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $transaction->commit();
            // $request->sendNotifyToManagementUser($user,null,Request::UPDATE_STATUS);
            $request->sendNotifyToResidentUser($user, null,Request::UPDATE_STATUS);
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Delete success"),
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
