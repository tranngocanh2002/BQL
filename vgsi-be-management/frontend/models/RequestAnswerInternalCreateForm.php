<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Request;
use common\models\RequestAnswerInternal;
use common\models\RequestMapAuthGroup;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="RequestAnswerInternalCreateForm")
 * )
 */
class RequestAnswerInternalCreateForm extends Model
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
            $requestMapAuthGroup = RequestMapAuthGroup::findOne(['request_id' => $this->request_id, 'auth_group_id' => $user->auth_group_id]);
            if(empty($requestMapAuthGroup)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $item = new RequestAnswerInternal();
            $item->load(CUtils::arrLoad($this->attributes), '');
            if (isset($this->attach) && is_array($this->attach)) {
                $item->attach = !empty($this->attach) ? json_encode($this->attach) : null;
            }
            $item->management_user_id = $user->id;
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
            $request = Request::findOne(['id' => $item->request_id]);
            $request->sendNotifyToManagementUser($user, null, Request::UPDATE, Request::ANSWER_INTERNAL);
            return RequestAnswerInternalResponse::findOne(['id' => $item->id]);
//            return [
//                'success' => true,
//                'message' => Yii::t('frontend', "Create success"),
//            ];
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

    public function update()
    {
        $item = RequestAnswerInternalResponse::findOne(['id' => (int)$this->id]);
        if ($item) {
            $item->load(CUtils::arrLoad($this->attributes), '');
            if (isset($this->attach) && is_array($this->attach)) {
                $item->attach = !empty($this->attach) ? json_encode($this->attach) : null;
            }
            if (!$item->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            return $item;
        } else {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
