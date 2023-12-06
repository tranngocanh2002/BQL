<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceProvider;
use common\models\ServiceProviderBillingInfo;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceProviderBillingInfoForm")
 * )
 */
class ServiceProviderBillingInfoForm extends Model
{
    /**
     * @SWG\Property(description="service_provider_id - bắt buộc", default=1, type="integer")
     * @var integer
     */
    public $service_provider_id;

    /**
     * @SWG\Property(description="Hướng dẫn thanh toán tiền mặt", default="", type="string")
     * @var string
     */
    public $cash_instruction;

    /**
     * @SWG\Property(description="Hướng dẫn chuyển khoản ngân hàng", default="", type="string")
     * @var string
     */
    public $transfer_instruction;

    /**
     * @SWG\Property(description="Tên ngân hàng", default="", type="string")
     * @var string
     */
    public $bank_name;

    /**
     * @SWG\Property(description="Số tài khoản ngân hàng", default="", type="string")
     * @var string
     */
    public $bank_number;

    /**
     * @SWG\Property(description="Chủ tài khoản", default="", type="string")
     * @var string
     */
    public $bank_holders;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_provider_id'], 'required'],
            [['service_provider_id'], 'integer'],
            [['cash_instruction', 'transfer_instruction', 'bank_name', 'bank_number', 'bank_holders'], 'string'],
            [['service_provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceProvider::className(), 'targetAttribute' => ['service_provider_id' => 'id']],
        ];
    }

    public function update()
    {
        $ServiceProviderBillingInfo = ServiceProviderBillingInfoResponse::findOne(['service_provider_id' => $this->service_provider_id]);
        if (empty($ServiceProviderBillingInfo)) {
            $ServiceProviderBillingInfo = new ServiceProviderBillingInfo();
        }
        $ServiceProviderBillingInfo->load(CUtils::arrLoad($this->attributes), '');
        if (!$ServiceProviderBillingInfo->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $ServiceProviderBillingInfo->getErrors()
            ];
        } else {
            return $ServiceProviderBillingInfo;
        }
    }

}
