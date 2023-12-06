<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\BuildingCluster;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="BuildingClusterCreateForm")
 * )
 */
class BuildingClusterCreateForm extends Model
{

    /**
     * @SWG\Property(description="Domain")
     * @var string
     */
    public $domain;

    /**
     * @SWG\Property(description="Name")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="Description")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(description="Email")
     * @var string
     */
    public $email;

    /**
     * @SWG\Property(description="Hotline")
     * @var string
     */
    public $hotline;

    /**
     * @SWG\Property(description="Address")
     * @var string
     */
    public $address;

    /**
     * @SWG\Property(description="Bank Account")
     * @var string
     */
    public $bank_account;

    /**
     * @SWG\Property(description="Bank Name")
     * @var string
     */
    public $bank_name;

    /**
     * @SWG\Property(description="Bank Holders")
     * @var string
     */
    public $bank_holders;

    /**
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $medias;

    /**
     * @SWG\Property(description="Tax Code")
     * @var string
     */
    public $tax_code;

    /**
     * @SWG\Property(description="Tax Info")
     * @var string
     */
    public $tax_info;

    /**
     * @SWG\Property(description="Alias")
     * @var string
     */
    public $alias;

    /**
     * @SWG\Property(description="cash instruction: hướng dẫn thanh toán chuyển khoản")
     * @var string
     */
    public $cash_instruction;

    /**
     * @SWG\Property(property="setting_group_receives_notices_financial", description="setting_group_receives_notices_financial : nhóm quyền nhận thông báo tài chính", type="array",
     *     @SWG\Items(type="integer"),
     * )
     * @var array
     */
    public $setting_group_receives_notices_financial;

    /**
     * @SWG\Property(description="City Id")
     * @var integer
     */
    public $city_id;

    /**
     * @SWG\Property(property="payment_config", type="object", ref="#/definitions/PaymentConfigForm" ),
     */
    public $payment_config;

    /**
     * @SWG\Property(description="link_whether")
     * @var string
     */
    public $link_whether;

    /**
     * @SWG\Property(description="email_account_push")
     * @var string
     */
    public $email_account_push;

    /**
     * @SWG\Property(description="email_password_push")
     * @var string
     */
    public $email_password_push;

    /**
     * @SWG\Property(description="sms_brandname_push")
     * @var string
     */
    public $sms_brandname_push;

    /**
     * @SWG\Property(description="sms_account_push")
     * @var string
     */
    public $sms_account_push;

    /**
     * @SWG\Property(description="sms_password_push")
     * @var string
     */
    public $sms_password_push;

    /**
     * @SWG\Property(description="message_request_default")
     * @var string
     */
    public $message_request_default;

    /**
     * @SWG\Property(property="service_bill_template", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $service_bill_template;

    /**
     * @SWG\Property(property="service_bill_invoice_template", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $service_bill_invoice_template;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [[ 'link_whether',
                'email_account_push',
                'email_password_push',
                'sms_brandname_push',
                'sms_account_push',
                'sms_password_push',
                'name', 'email', 'description', 'hotline', 'address', 'bank_account', 'bank_name', 'bank_holders', 'tax_code', 'tax_info', 'cash_instruction', 'message_request_default', 'alias'], 'string'],
            [['medias', 'city_id', 'setting_group_receives_notices_financial', 'payment_config','service_bill_template', 'service_bill_invoice_template'], 'safe'],
            [['domain'], 'required', "on" => ['update']],
            [['domain'], 'string', "on" => ['update']],
        ];
    }

    public function create(){
        $item = new BuildingCluster();
        $item->load(CUtils::arrLoad($this->attributes),'');
        if (isset($this->medias) && is_array($this->medias)) {
            $item->medias = !empty($this->medias) ? json_encode($this->medias) : null;
        }
        if (isset($this->setting_group_receives_notices_financial) && is_array($this->setting_group_receives_notices_financial)) {
            $item->setting_group_receives_notices_financial = !empty($this->setting_group_receives_notices_financial) ? json_encode($this->setting_group_receives_notices_financial) : null;
        }
        if (isset($this->service_bill_template) && is_array($this->service_bill_template)) {
            $item->service_bill_template = !empty($this->service_bill_template) ? json_encode($this->service_bill_template) : null;
        }
        if (isset($this->service_bill_invoice_template) && is_array($this->service_bill_invoice_template)) {
            $item->service_bill_invoice_template = !empty($this->service_bill_invoice_template) ? json_encode($this->service_bill_invoice_template) : null;
        }
        if(!$item->save()){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $item->getErrors()
            ];
        }else{
            if(!empty($this->payment_config) && is_array($this->payment_config)){
                $payConfig = new PaymentConfigForm();
                $payConfig->load($this->payment_config, '');
                $payConfig->building_cluster_id = $item->id;
                $payRes = $payConfig->main();
                if(empty($payRes['success'])){
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "create payment config error"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Create success"),
            ];
        }
    }

    public function update(){
        $item = BuildingClusterResponse::findOne(['domain' => $this->domain]);
        if($item){
            $item->load(CUtils::arrLoad($this->attributes), '');
            if (isset($this->medias) && is_array($this->medias)) {
                $item->medias = !empty($this->medias) ? json_encode($this->medias) : null;
            }
            if (isset($this->setting_group_receives_notices_financial) && is_array($this->setting_group_receives_notices_financial)) {
                $item->setting_group_receives_notices_financial = !empty($this->setting_group_receives_notices_financial) ? json_encode($this->setting_group_receives_notices_financial) : null;
            }
            if (isset($this->service_bill_template) && is_array($this->service_bill_template)) {
                $item->service_bill_template = !empty($this->service_bill_template) ? json_encode($this->service_bill_template) : null;
            }
            if (isset($this->service_bill_invoice_template) && is_array($this->service_bill_invoice_template)) {
                $item->service_bill_invoice_template = !empty($this->service_bill_invoice_template) ? json_encode($this->service_bill_invoice_template) : null;
            }
            if(!$item->save()){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }else{
                if(!empty($this->payment_config) && is_array($this->payment_config)){
                    $payConfig = new PaymentConfigForm();
                    $payConfig->load($this->payment_config, '');
                    $payConfig->building_cluster_id = $item->id;
                    $payRes = $payConfig->main();
                    if(empty($payRes['success'])){
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "create payment config error"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        ];
                    }
                }
                return $item;
            }
        }else{
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
