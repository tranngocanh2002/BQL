<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\Request;
use common\models\RequestAnswer;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="RequestAnswerCreateForm")
 * )
 */
class RequestAnswerCreateForm extends Model
{
    /**
     * @SWG\Property(description="Id - Bắt buộc khi update", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Request Id")
     * @var integer
     */
    public $request_id;

    /**
     * @SWG\Property(description="Content")
     * @var string
     */
    public $content;

    /**
     * @SWG\Property(property="attach", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $attach;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'content'], 'required'],
            [['content'], 'string'],
            [['request_id'], 'integer'],
            [['attach'], 'safe'],
            [['id'], 'required', "on" => ['update']],
            [['id'], 'integer', "on" => ['update']],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            //check quyen tra loi
            $request = Request::findOne(['id' => $this->request_id]);
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $request->apartment_id, 'resident_user_phone' => $user->phone, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($apartmentMapResidentUser)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $item = new RequestAnswer();
            $item->load(CUtils::arrLoad($this->attributes), '');
            if (isset($this->attach) && is_array($this->attach)) {
                $item->attach = !empty($this->attach) ? json_encode($this->attach) : null;
            }
            $item->resident_user_id = $user->id;
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
            $request = Request::findOne(['id' => $item->request_id]);
//            $request->sendNotifyToManagementUser(null, $user, Request::UPDATE, Request::ANSWER_NOT_INTERNAL);
            $request->sendNotifyToResidentUser(null, $user, Request::RESIDENT_CREATE_COMMENT);
            return RequestAnswerResponse::findOne(['id' => $item->id]);
//            return [
//                'success' => true,
//                'message' => Yii::t('resident', "Create success"),
//            ];
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

    public function update()
    {
        $user = Yii::$app->user->getIdentity();
        $item = RequestAnswer::findOne(['id' => (int)$this->id, 'resident_user_id' => $user->id]);
        if ($item) {
            $item->load(CUtils::arrLoad($this->attributes), '');
            if (isset($this->attach) && is_array($this->attach)) {
                $item->attach = !empty($this->attach) ? json_encode($this->attach) : null;
            }
            if (!$item->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            return [
                'success' => true,
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
