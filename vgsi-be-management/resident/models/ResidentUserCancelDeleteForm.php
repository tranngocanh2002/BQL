<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ResidentUser;
use resident\models\ResidentUserResponse;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserCancelDeleteForm")
 * )
 */
class ResidentUserCancelDeleteForm extends Model
{
    public function cancelDelete()
    {
        $user = Yii::$app->user->getIdentity();
        $item = ResidentUserResponse::findOne(['id' => $user->id, 'is_deleted' => ResidentUser::NOT_DELETED]);
        if ($item) {
            if (empty($item->deleted_at)) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid Invalid"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            } else if ($item->deleted_at < time() - 15 * 24 * 60 * 60) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid Invalid"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $item->deleted_at = null;
            $item->save();
            return [
                'success' => true,
                'message' => Yii::t('resident', "Success"),
            ];
        } else {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
