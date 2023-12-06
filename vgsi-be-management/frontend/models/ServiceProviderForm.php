<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceMapManagement;
use common\models\ServiceProvider;
use common\models\ManagementUser;
use common\models\ServiceProviderBillingInfo;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceProviderForm")
 * )
 */
class ServiceProviderForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Name nhà cung cấp", default="Nhà cung cấp", type="string")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="Address", default="", type="string")
     * @var string
     */
    public $address;
    
    /**
     * @SWG\Property(description="Description", default="", type="string")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(description="Status", default=0, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="Using Bank Cluster: 0- sử dụng tài khoản ngân hàng riêng, 1 - sử dụng tài khoản ngân hàng của ban quản lý", default=1, type="integer")
     * @var integer
     */
    public $using_bank_cluster;

    /**
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $medias;

    /**
     * @SWG\Property(property="billing_info", type="object",
     *     ref="#/definitions/ServiceProviderBillingInfoForm"
     * ),
     * @var array
     */
    public $billing_info;

    /**
     * @SWG\Property(property="payment_config", type="object", ref="#/definitions/PaymentConfigForm" ),
     */
    public $payment_config;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required', "on" => ['update', 'delete']],
            [['id', 'status', 'using_bank_cluster'], 'integer'],
            [['name', 'address', 'description'], 'string'],
            [['medias', 'billing_info', 'payment_config'], 'safe'],
        ];
    }

    public function create()
    {
        $user = Yii::$app->user->getIdentity();
        $serviceProvider = new ServiceProvider();
        $serviceProvider->load(CUtils::arrLoad($this->attributes), '');
        $serviceProvider->building_cluster_id = $user->building_cluster_id;
        if (isset($this->medias) && is_array($this->medias)) {
            $serviceProvider->medias = !empty($this->medias) ? json_encode($this->medias) : null;
        }
        if (!$serviceProvider->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $serviceProvider->getErrors()
            ];
        } else {
            if(!empty($this->billing_info)){
                $ServiceProviderBillingInfo = new ServiceProviderBillingInfo();
                $ServiceProviderBillingInfo->load(CUtils::arrLoad($this->billing_info), '');
                $ServiceProviderBillingInfo->service_provider_id = $serviceProvider->id;
                if (!$ServiceProviderBillingInfo->save()) {
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $ServiceProviderBillingInfo->getErrors()
                    ];
                }
            }
            if(!empty($this->payment_config) && is_array($this->payment_config)){
                $payConfig = new PaymentConfigForm();
                $payConfig->load($this->payment_config, '');
                $payConfig->building_cluster_id = $serviceProvider->building_cluster_id;
                $payConfig->service_provider_id = $serviceProvider->id;
                $payRes = $payConfig->main();
                if(empty($payRes['success'])){
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "create payment config error"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }
            return ServiceProviderResponse::findOne(['id' => $serviceProvider->id]);
        }
    }

    public function update()
    {
        $serviceProvider = ServiceProviderResponse::findOne(['id' => (int)$this->id]);
        if ($serviceProvider) {
            $serviceProvider->load(CUtils::arrLoad($this->attributes), '');
            if (isset($this->medias) && is_array($this->medias)) {
                $serviceProvider->medias = !empty($this->medias) ? json_encode($this->medias) : null;
            }
            if (!$serviceProvider->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $serviceProvider->getErrors()
                ];
            } else {
                if(!empty($this->billing_info)){
                    $ServiceProviderBillingInfo = ServiceProviderBillingInfo::findOne(['service_provider_id' => $serviceProvider->id]);
                    if(empty($ServiceProviderBillingInfo)){
                        $ServiceProviderBillingInfo = new ServiceProviderBillingInfo();
                    }
                    $ServiceProviderBillingInfo->load(CUtils::arrLoad($this->billing_info), '');
                    $ServiceProviderBillingInfo->service_provider_id = $serviceProvider->id;
                    if (!$ServiceProviderBillingInfo->save()) {
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                            'errors' => $ServiceProviderBillingInfo->getErrors()
                        ];
                    }
                }
                if(!empty($this->payment_config) && is_array($this->payment_config)){
                    $payConfig = new PaymentConfigForm();
                    $payConfig->load($this->payment_config, '');
                    $payConfig->building_cluster_id = $serviceProvider->building_cluster_id;
                    $payConfig->service_provider_id = $serviceProvider->id;
                    $payRes = $payConfig->main();
                    if(empty($payRes['success'])){
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "create payment config error"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        ];
                    }
                }
                return $serviceProvider;
            }
        } else {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function delete()
    {
        if(!$this->id){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $serviceMapManagement = ServiceMapManagement::findOne(['service_provider_id' => $this->id, 'is_deleted' => ServiceMapManagement::NOT_DELETED]);
        if(!empty($serviceMapManagement)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Provider contains service, not deleted"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $serviceProvider = ServiceProvider::findOne($this->id);
        $serviceProvider->is_deleted = ServiceProvider::DELETED;
        if($serviceProvider->save()){
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Delete Success")
            ];
        }else{
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

}
