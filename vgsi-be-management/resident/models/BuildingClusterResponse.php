<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\BuildingCluster;
use common\models\City;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="BuildingClusterResponse")
 * )
 */
class BuildingClusterResponse extends BuildingCluster
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="domain", type="string"),
     * @SWG\Property(property="link_dksd", type="string"),
     * @SWG\Property(property="email", type="string"),
     * @SWG\Property(property="hotline", type="string"),
     * @SWG\Property(property="address", type="string"),
     * @SWG\Property(property="bank_account", type="string"),
     * @SWG\Property(property="bank_name", type="string"),
     * @SWG\Property(property="bank_holders", type="string"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="tax_code", type="string"),
     * @SWG\Property(property="tax_info", type="string"),
     * @SWG\Property(property="city_id", type="integer"),
     * @SWG\Property(property="city_name", type="string"),
     * @SWG\Property(property="message_request_default", type="string"),
     * @SWG\Property(property="alias", type="string"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'description',
            'domain',
            'link_dksd',
            'email',
            'hotline',
            'address',
            'bank_account',
            'bank_name',
            'bank_holders',
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias) : null;
            },
            'tax_code',
            'tax_info',
            'city_id',
            'city_name' => function ($model) {
                if (!empty($model->city_id)) {
                    $city = City::findOne(['id' => $model->city_id]);
                    return $city->name;
                }
                return '';
            },
            'message_request_default',
            'alias',
            'created_at',
            'updated_at',
        ];
    }
}
