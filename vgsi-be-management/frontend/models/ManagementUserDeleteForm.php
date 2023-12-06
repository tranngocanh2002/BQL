<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ManagementUser;
use common\models\ManagementUserAccessToken;
use common\models\ManagementUserDeviceToken;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementUserDeleteForm")
 * )
 */
class ManagementUserDeleteForm extends Model
{
    /**
     * @SWG\Property(description="Id", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => ManagementUser::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function delete()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $item = ManagementUser::findOne(['id' => $this->id, 'role_type' => ManagementUser::DEFAULT_ADMIN]);
            $item->is_deleted = ManagementUser::DELETED;
            $emails = explode('@', $item->email);
            $item->email = $emails[0] . '_' . time().'@'.$emails[1];
            if (!$item->save()) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "System busy"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            ManagementUserDeviceToken::deleteAll(['management_user_id' => $item->id]);
            ManagementUserAccessToken::deleteAll(['management_user_id' => $item->id]);
            $transaction->commit();
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
