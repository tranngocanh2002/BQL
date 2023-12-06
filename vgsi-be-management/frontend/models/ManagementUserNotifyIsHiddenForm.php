<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ManagementUserNotify;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementUserNotifyIsHiddenForm")
 * )
 */
class ManagementUserNotifyIsHiddenForm extends Model
{

    const IS_HIDDEN_ALL = 1;
    const IS_NOT_HIDDEN_ALL = 1;
    /**
     * @SWG\Property(description="is_hidden_all = 1 là đánh dấu đã đọc tất cả, is_hidden_all = 0 thì sẽ check theo mảng is_hidden_array")
     * @var integer
     */
    public $is_hidden_all;

    /**
     * @SWG\Property(description="is_hidden_array : mảng các id đánh dấu là đã đọc", type="array",
     *      @SWG\Items(type="integer", default=0),
     * )
     * @var array
     */
    public $is_hidden_array;

    /**
     * @SWG\Property(description="is_not_hidden_all = 1 là đánh dấu chưa đọc tất cả, is_not_hidden_all = 0 thì sẽ check theo mảng is_not_hidden_array")
     * @var integer
     */
    public $is_not_hidden_all;

    /**
     * @SWG\Property(description="is_not_hidden_array : mảng các id đánh dấu là chưa đọc", type="array",
     *      @SWG\Items(type="integer", default=0),
     * )
     * @var array
     */
    public $is_not_hidden_array;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_hidden_all', 'is_not_hidden_all'], 'integer'],
            [['is_hidden_array', 'is_not_hidden_array'], 'safe'],
        ];
    }

    public function isHidden()
    {
        try {
            $user = Yii::$app->user->getIdentity();
            if ($this->is_hidden_all == self::IS_HIDDEN_ALL && $this->is_not_hidden_all == self::IS_NOT_HIDDEN_ALL) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if (
                $this->is_hidden_all != self::IS_HIDDEN_ALL
                && $this->is_not_hidden_all != self::IS_NOT_HIDDEN_ALL
                && (empty($this->is_hidden_array) || !is_array($this->is_hidden_array))
                && (empty($this->is_not_hidden_array) || !is_array($this->is_not_hidden_array))
            ) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if ($this->is_hidden_all == self::IS_HIDDEN_ALL) {
                ManagementUserNotify::updateAll(['is_hidden' => ManagementUserNotify::IS_HIDDEN], ['management_user_id' => $user->id, 'is_hidden' => ManagementUserNotify::IS_NOT_HIDDEN]);
            } else {
                if (!empty($this->is_hidden_array) && is_array($this->is_hidden_array)) {
                    ManagementUserNotify::updateAll(['is_hidden' => ManagementUserNotify::IS_HIDDEN], ['id' => $this->is_hidden_array, 'management_user_id' => $user->id, 'is_hidden' => ManagementUserNotify::IS_NOT_HIDDEN]);
                }
            }

            if ($this->is_not_hidden_all == self::IS_NOT_HIDDEN_ALL) {
                ManagementUserNotify::updateAll(['is_hidden' => ManagementUserNotify::IS_NOT_HIDDEN], ['management_user_id' => $user->id, 'is_hidden' => ManagementUserNotify::IS_HIDDEN]);
            } else {
                if (!empty($this->is_not_hidden_array) && is_array($this->is_not_hidden_array)) {
                    ManagementUserNotify::updateAll(['is_hidden' => ManagementUserNotify::IS_NOT_HIDDEN], ['id' => $this->is_not_hidden_array, 'management_user_id' => $user->id, 'is_hidden' => ManagementUserNotify::IS_HIDDEN]);
                }
            }
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Update success"),
            ];
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

}
