<?php

namespace resident\models;

use common\helpers\ApiHelper;
use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ResidentUserAccessToken;
use common\models\ResidentUserDeviceToken;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserDeviceTokenCreateForm")
 * )
 */
class ResidentUserDeviceTokenCreateForm extends Model
{
    /**
     * @SWG\Property(description="Device Token")
     * @var string
     */
    public $device_token;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['device_token'], 'required'],
            [['device_token'], 'string'],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $item = ResidentUserDeviceToken::findOne(['resident_user_id' => $user->id, 'device_token' => $this->device_token]);
            if(empty($item)){
                $item = new ResidentUserDeviceToken();
                $item->resident_user_id = $user->id;
            }
            $item->load(CUtils::arrLoad($this->attributes), '');

            //lấy id access token để map vào đây
            $accessToken = ApiHelper::getAuthorization();
            $residentUserAccessToken = ResidentUserAccessToken::findOne(['token_hash' => md5($accessToken), 'type' => ResidentUserAccessToken::TYPE_ACCESS_TOKEN]);
            if(!empty($residentUserAccessToken)){
                $item->resident_user_access_token_id = $residentUserAccessToken->id;
            }
            if (!$item->save()) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('resident', "Create success"),
            ];
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => Yii::t('resident', "System busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                // 'errors' => $ex->getMessage()
            ];
        }
    }
}
