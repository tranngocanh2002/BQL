<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\Request;
use Yii;
use yii\base\Model;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="RequestRateForm")
 * )
 */
class RequestRateForm extends Model
{
    /**
     * @SWG\Property(description="Id - Bắt buộc", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Rate")
     * @var number
     */
    public $rate;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['rate'], 'safe'],
        ];
    }

    public function rate()
    {
        $user = Yii::$app->user->getIdentity();
        $item = RequestResponse::find()
            ->where([
                'id' => (int)$this->id,
                'resident_user_id' => $user->id
            ])
            ->andWhere(['OR',
                ['status' => Request::STATUS_COMPLETE],
                ['status' => Request::STATUS_CLOSE, 'type_close' => Request::TYPE_BQL_CLOSE]
            ])
            ->one();
        if ($item) {
            $item->status = Request::STATUS_CLOSE;
            $item->rate = $this->rate;
            if (!$item->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            // $item->sendNotifyToManagementUser(null,$user,Request::UPDATE_STATUS, Request::STATUS_CLOSE);
            return [
                'success' => true,
                'message' => Yii::t('resident', "Rate success"),
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
