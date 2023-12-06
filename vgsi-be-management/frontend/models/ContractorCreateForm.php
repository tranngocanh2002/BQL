<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Contractor;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ContractorCreateForm")
 * )
 */
class ContractorCreateForm extends Model
{
    /**
     * @SWG\Property(description="Id - Bắt buộc khi update", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Status: 0 - ngừng hoạt động, 1 - đang hoạt động", default=1, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="name: tên nhà thầu")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="address: địa chỉ")
     * @var string
     */
    public $address;

    /**
     * @SWG\Property(description="description: mô tả")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(property="attach", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $attach;

    /**
     * @SWG\Property(description="contact_name: tên người liên hệ")
     * @var string
     */
    public $contact_name;

    /**
     * @SWG\Property(description="contact_phone: điện thoại ngươời liên hệ")
     * @var string
     */
    public $contact_phone;

    /**
     * @SWG\Property(description="contact_email: email người liên hệ")
     * @var string
     */
    public $contact_email;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update']],
            [['name', 'address', 'description', 'contact_name', 'contact_phone', 'contact_email'], 'string'],
            [['id', 'status'], 'integer'],
            [['attach'], 'safe'],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $item = new Contractor();
            $item->load(CUtils::arrLoad($this->attributes), '');
            $checkName = Contractor::findOne(['building_cluster_id' => $user->building_cluster_id, 'name' => $item->name, 'is_deleted' => Contractor::NOT_DELETED]);
            if(!empty($checkName)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Tên nhà thầu đã tồn tại"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            if (isset($this->attach) && is_array($this->attach)) {
                $item->attach = json_encode($this->attach);
            }
            $item->status = Contractor::STATUS_ON;
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
            return ContractorResponse::findOne(['id' => $item->id]);
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
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $item = Contractor::findOne(['id' => (int)$this->id, 'building_cluster_id' => $user->building_cluster_id, 'is_deleted' => Contractor::NOT_DELETED]);
            if ($item) {
                $item->load(CUtils::arrLoad($this->attributes), '');
                $checkName = Contractor::find()->where([
                    'building_cluster_id' => $user->building_cluster_id,
                    'name' => $item->name,
                    'is_deleted' => Contractor::NOT_DELETED
                ])->andWhere(['<>', 'id', $item->id])->one();
                if(!empty($checkName)){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Tên nhà thầu đã tồn tại"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                }
                if (isset($this->attach) && is_array($this->attach)) {
                    $item->attach = json_encode($this->attach);
                }
                if (!$item->save()) {
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                }
                $transaction->commit();
                return ContractorResponse::findOne(['id' => $item->id]);
            } else {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
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
