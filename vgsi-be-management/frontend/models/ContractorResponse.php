<?php

namespace frontend\models;

use common\models\Contractor;
use common\models\Job;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ContractorResponse")
 * )
 */
class ContractorResponse extends Contractor
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="address", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="contact_name", type="string"),
     * @SWG\Property(property="contact_phone", type="string"),
     * @SWG\Property(property="contact_email", type="string"),
     * @SWG\Property(property="status", type="integer", description="0 : ngừng hoạt động, 1: đang hoạt động"),
     * @SWG\Property(property="attach", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'address',
            'description',
            'contact_name',
            'contact_phone',
            'contact_email',
            'attach' => function ($model) {
                return (!empty($model->attach)) ? json_decode($model->attach) : null;
            },
            'status',
            'created_at',
            'updated_at',
        ];
    }
}
