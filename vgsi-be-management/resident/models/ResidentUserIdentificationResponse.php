<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ResidentUserIdentification;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserIdentificationResponse")
 * )
 */
class ResidentUserIdentificationResponse extends ResidentUserIdentification
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="building_cluster_id", type="integer"),
     * @SWG\Property(property="status", type="integer", description="0 - chưa xác thực, 1- đã xác thực"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     */
    public function fields()
    {
        return [
            'id',
            'building_cluster_id',
            'status',
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias) : null;
            },
        ];
    }
}
