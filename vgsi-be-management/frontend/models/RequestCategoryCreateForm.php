<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\RequestCategory;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="RequestCategoryCreateForm")
 * )
 */
class RequestCategoryCreateForm extends Model
{
    /**
     * @SWG\Property(description="Id - Bắt buộc khi update", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Type", default="", type="integer")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="Name")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="Name En")
     * @var string
     */
    public $name_en;

    /**
     * @SWG\Property(description="Color")
     * @var string
     */
    public $color;

    /**
     * @SWG\Property(property="auth_group_ids", type="array",
     *     @SWG\Items(type="integer", default=1),
     * ),
     * @var array
     */
    public $auth_group_ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'color'], 'required'],
            [['name', 'name_en', 'color'], 'string'],
            [['auth_group_ids'], 'safe'],
            [['id'], 'required', "on" => ['update']],
            [['id'], 'integer', "on" => ['update']],
            [['type'], 'integer'],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $item = new RequestCategory();
            $item->load(CUtils::arrLoad($this->attributes), '');
            if (!empty($this->auth_group_ids)) {
                $item->auth_group_ids = json_encode($this->auth_group_ids);
            }
            $item->building_cluster_id = $user->building_cluster_id;
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

    public function update()
    {
        $item = RequestCategoryResponse::findOne(['id' => (int)$this->id]);
        if ($item) {
            $item->load(CUtils::arrLoad($this->attributes), '');
            if (!empty($this->auth_group_ids)) {
                $item->auth_group_ids = json_encode($this->auth_group_ids);
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
