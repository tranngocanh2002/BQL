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
 *   @SWG\Xml(name="ManagementUserNotifyIsReadForm")
 * )
 */
class ManagementUserNotifyIsReadForm extends Model
{

    const IS_READ_ALL = 1;
    const IS_UNREAD_ALL = 1;
    /**
     * @SWG\Property(description="is_read_all = 1 là đánh dấu đã đọc tất cả, is_read_all = 0 thì sẽ check theo mảng is_read_array")
     * @var integer
     */
    public $is_read_all;

    /**
     * @SWG\Property(description="is_read_array : mảng các id đánh dấu là đã đọc", type="array",
     *      @SWG\Items(type="integer", default=0),
     * )
     * @var array
     */
    public $is_read_array;

    /**
     * @SWG\Property(description="is_unread_all = 1 là đánh dấu chưa đọc tất cả, is_unread_all = 0 thì sẽ check theo mảng is_unread_array")
     * @var integer
     */
    public $is_unread_all;

    /**
     * @SWG\Property(description="is_unread_array : mảng các id đánh dấu là chưa đọc", type="array",
     *      @SWG\Items(type="integer", default=0),
     * )
     * @var array
     */
    public $is_unread_array;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_read_all', 'is_unread_all'], 'integer'],
            [['is_read_array', 'is_unread_array'], 'safe'],
        ];
    }

    public function isRead()
    {
        try {
            $user = Yii::$app->user->getIdentity();
            if ($this->is_read_all == self::IS_READ_ALL && $this->is_unread_all == self::IS_UNREAD_ALL) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if (
                $this->is_read_all != self::IS_READ_ALL
                && $this->is_unread_all != self::IS_UNREAD_ALL
                && (empty($this->is_read_array) || !is_array($this->is_read_array))
                && (empty($this->is_unread_array) || !is_array($this->is_unread_array))
            ) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if ($this->is_read_all == self::IS_READ_ALL) {
                ManagementUserNotify::updateAll(['is_read' => ManagementUserNotify::IS_READ], ['management_user_id' => $user->id, 'is_read' => ManagementUserNotify::IS_UNREAD]);
            } else {
                if (!empty($this->is_read_array) && is_array($this->is_read_array)) {
                    ManagementUserNotify::updateAll(['is_read' => ManagementUserNotify::IS_READ], ['id' => $this->is_read_array, 'management_user_id' => $user->id, 'is_read' => ManagementUserNotify::IS_UNREAD]);
                }
            }

            if ($this->is_unread_all == self::IS_UNREAD_ALL) {
                ManagementUserNotify::updateAll(['is_read' => ManagementUserNotify::IS_UNREAD], ['management_user_id' => $user->id, 'is_read' => ManagementUserNotify::IS_READ]);
            } else {
                if (!empty($this->is_unread_array) && is_array($this->is_unread_array)) {
                    ManagementUserNotify::updateAll(['is_read' => ManagementUserNotify::IS_UNREAD], ['id' => $this->is_unread_array, 'management_user_id' => $user->id, 'is_read' => ManagementUserNotify::IS_READ]);
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
