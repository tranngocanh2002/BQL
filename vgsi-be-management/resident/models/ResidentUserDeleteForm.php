<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserDeleteForm")
 * )
 */
class ResidentUserDeleteForm extends Model
{
    /**
     * @SWG\Property(description="password")
     * @var string
     */
    public $password;

    /**
     * @SWG\Property(description="reason")
     * @var string
     */
    public $reason;

    public function rules()
    {
        return [
            [['password', 'reason'], 'required'],
        ];
    }

    public function delete(){
        $user = Yii::$app->user->getIdentity();
        $item = ResidentUserResponse::findOne(['id' => $user->id]);
        if($item){
            if(!$item->validatePassword($this->password)){
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Password Invalid"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            if($item->is_deleted == ResidentUser::DELETED){
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Trạng thái không phù hợp"),
                ];
            }else{
//               $item->is_deleted = ResidentUser::DELETED;
               $item->deleted_at = time();
               $item->reason = $this->reason;
               $item->save();
                return [
                    'success' => true,
                    'message' => Yii::t('resident', "Success"),
                ];
            }
        }else{
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
