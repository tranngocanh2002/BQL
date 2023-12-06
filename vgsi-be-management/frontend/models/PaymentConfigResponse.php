<?php

namespace frontend\models;

use common\models\Post;
use common\models\PaymentConfig;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PaymentConfigResponse")
 * )
 */
class PaymentConfigResponse extends PaymentConfig
{
    /**
     * @SWG\Property(property="receiver_account", type="string"),
     * @SWG\Property(property="merchant_id", type="string"),
     * @SWG\Property(property="merchant_pass", type="string"),
     * @SWG\Property(property="status", type="integer", description="0- chưa kích hoạt, 1 - đã kích hoạt"),
     */
    public function fields()
    {
        return [
            'receiver_account',
            'merchant_id',
            'merchant_pass',
            'status',
        ];
    }
}
