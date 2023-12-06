<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\ResidentUserMapRead;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserMapReadResponse")
 * )
 */
class ResidentUserMapReadResponse extends ResidentUserMapRead
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="building_cluster_id", type="integer"),
     * @SWG\Property(property="type", type="integer", description="0 - Request, 1 - announcement, 2 - payment fee ..."),
     * @SWG\Property(property="is_read", type="integer", description="0 - Chưa đọc, 1 - đã đọc"),
     * @SWG\Property(property="resident_user_id", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'building_cluster_id',
            'type',
            'is_read',
            'resident_user_id',
        ];
    }
}
