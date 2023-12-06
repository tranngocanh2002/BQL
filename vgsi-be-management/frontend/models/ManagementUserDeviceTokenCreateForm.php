<?php

namespace frontend\models;

use common\helpers\ApiHelper;
use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ManagementUserAccessToken;
use common\models\ManagementUserDeviceToken;
use common\models\ResidentUserAccessToken;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementUserDeviceTokenCreateForm")
 * )
 */
class ManagementUserDeviceTokenCreateForm extends Model
{
    /**
     * @SWG\Property(description="Device Token")
     * @var string
     */
    public $device_token;

    public $type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['device_token'], 'required'],
            [['type'], 'integer'],
            [['device_token'], 'string'],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $is_web = ($this->type) ? 1 : 0 ;
            $item = ManagementUserDeviceToken::findOne(['management_user_id' => $user->id, 'device_token' => $this->device_token,'type'=>$is_web]);
            if(empty($item)){
                $item = new ManagementUserDeviceToken();
                $item->management_user_id   = $user->id;
                $item->type                 = $is_web;
            }
            $item->load(CUtils::arrLoad($this->attributes), '');

            //lấy id access token để map vào đây
            $accessToken = ApiHelper::getAuthorization();
            $managementUserAccessToken = ManagementUserAccessToken::findOne(['token_hash' => md5($accessToken) ,'type' => ManagementUserAccessToken::TYPE_ACCESS_TOKEN]);
            if(!empty($managementUserAccessToken)){
                $item->management_user_access_token_id = $managementUserAccessToken->id;
            }

            if (!$item->save()) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Create success"),
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
