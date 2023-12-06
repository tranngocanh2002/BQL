<?php

namespace frontend\models;

use common\models\ManagementUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="PerformerResponse")
 * )
 */
class PerformerResponse extends ManagementUser
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="email", type="string"),
     * @SWG\Property(property="phone", type="string"),
     * @SWG\Property(property="first_name", type="string"),
     * @SWG\Property(property="last_name", type="string"),
     * @SWG\Property(property="avatar", type="string"),
     * @SWG\Property(property="gender", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'email',
            'phone',
            'first_name',
            'last_name',
            'avatar',
            'gender',
        ];
    }
}
