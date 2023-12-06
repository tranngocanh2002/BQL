<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserResponseByPhone")
 * )
 */
class ResidentUserResponseByPhone extends ResidentUser
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="phone", type="string"),
     * @SWG\Property(property="first_name", type="string"),
     * @SWG\Property(property="last_name", type="string"),
     */


    public function fields()
    {
        return [
            'id',
            'phone',
            'first_name',
            'first_name',
        ];
    }
}
