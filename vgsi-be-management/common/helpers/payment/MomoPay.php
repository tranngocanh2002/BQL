<?php
/**
 * Created by PhpStorm.
 * User: lucnn
 * Email: nguyennhuluc1990@gmail.com
 * Phone: 0961196368
 */

namespace common\helpers\payment;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\helpers\MyCurl;
use common\helpers\payment\form\MomoConfirmForm;
use common\helpers\payment\form\MomoRefundForm;
use common\helpers\payment\form\MomoRequestForm;
use Yii;
use yii\helpers\Json;

class MomoPay
{

    const PUBLIC_KEY = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkpa+qMXS6O11x7jBGo9W3yxeHEsAdyDE
40UoXhoQf9K6attSIclTZMEGfq6gmJm2BogVJtPkjvri5/j9mBntA8qKMzzanSQaBEbr8FyByHnf
226dsLt1RbJSMLjCd3UC1n0Yq8KKvfHhvmvVbGcWfpgfo7iQTVmL0r1eQxzgnSq31EL1yYNMuaZj
pHmQuT24Hmxl9W9enRtJyVTUhwKhtjOSOsR03sMnsckpFT9pn1/V9BE2Kf3rFGqc6JukXkqK6ZW9
mtmGLSq3K+JRRq2w8PVmcbcvTr/adW4EL2yc1qk9Ec4HtiDhtSYd6/ov8xLVkKAQjLVt7Ex3/agR
PfPrNwIDAQAB
-----END PUBLIC KEY-----";

    private static $_instance;

    private $ch;
    private $base_url;
    private $request;
    private $confirm;
    private $version;
    private $payType;

    private function __construct()
    {
        $this->ch = new MyCurl();
        $PaymentConfig = \Yii::$app->params['PaymentConfig'];
        $this->base_url = $PaymentConfig['momo']['base_url'];
        $this->request = $PaymentConfig['momo']['request'];
        $this->confirm = $PaymentConfig['momo']['confirm'];
        $this->version = $PaymentConfig['momo']['version'];
        $this->payType = $PaymentConfig['momo']['payType'];
    }

    public static function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new MomoPay();
        }
        return self::$_instance;
    }

    /**
     * @param MomoRequestForm $pay_request
     */
    public function payApp(MomoRequestForm $pay_request)
    {
        try {
            $pay_request->version = $this->version;
            $pay_request->payType = $this->payType;
            $pay_request->setHash();
            if (!$pay_request->validate()) {
                Yii::error($pay_request->errors);
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "System Busy"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $this->ch->headers = [
                'Content-Type' => 'application/json',
            ];
            $body = Json::encode($pay_request->toArray());
            $response = $this->ch->post($this->base_url . $this->request, $body);
            if (!empty($response->body)) {
                Yii::info($response->body);
                $body = Json::decode($response->body, true);
                if ($body['status'] == 0) {
                    $body['success'] = true;
                } else {
                    $body['errorCode'] = $body['status'];
                    $body['success'] = false;
                }
                return $body;
            }
            return [
                'success' => false,
                'message' => Yii::t('resident', "System Busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
    }

    /**
     * @param MomoConfirmForm $pay_confirm
     */
    public function confirm(MomoConfirmForm $pay_confirm)
    {
        try {
            $pay_confirm->setSignature();
            if (!$pay_confirm->validate()) {
                Yii::error($pay_confirm->errors);
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "System Busy"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $this->ch->headers = [
                'Content-Type' => 'application/json',
            ];
            $body = Json::encode($pay_confirm->toArray());
            $response = $this->ch->post($this->base_url . $this->confirm, $body);
            if (!empty($response->body)) {
                Yii::info($response->body);
                $body = Json::decode($response->body, true);
                if ($body['status'] == 0) {
                    $body['success'] = true;
                } else {
                    $body['errorCode'] = $body['status'];
                    $body['success'] = false;
                }
                return $body;
            }
            return [
                'success' => false,
                'message' => Yii::t('resident', "System Busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
    }

    /**
     * @param MomoRefundForm $pay_refund
     */
    public function refund(MomoRefundForm $pay_refund)
    {
        try {
            $pay_refund->version = $this->version;
            $pay_refund->setHash();
            if (!$pay_refund->validate()) {
                Yii::error($pay_refund->errors);
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "System Busy"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $this->ch->headers = [
                'Content-Type' => 'application/json',
            ];
            $body = Json::encode($pay_refund->toArray());
            $response = $this->ch->post($this->base_url . $this->request, $body);
            if (!empty($response->body)) {
                Yii::info($response->body);
                $body = Json::decode($response->body, true);
                if ($body['status'] == 0) {
                    $body['success'] = true;
                } else {
                    $body['errorCode'] = $body['status'];
                    $body['success'] = false;
                }
                return $body;
            }
            return [
                'success' => false,
                'message' => Yii::t('resident', "System Busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
    }
}
