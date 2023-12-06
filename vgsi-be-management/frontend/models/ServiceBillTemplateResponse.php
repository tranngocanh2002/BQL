<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceBillTemplate;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBillTemplateResponse")
 * )
 */
class ServiceBillTemplateResponse extends ServiceBillTemplate
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="style", type="string"),
     * @SWG\Property(property="content", type="string"),
     * @SWG\Property(property="sub_content", type="string"),
     */
    public function fields()
    {
        return [
            'id',
            'style',
            'content',
            'sub_content',
        ];
    }
}
