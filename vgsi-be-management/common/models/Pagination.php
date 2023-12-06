<?php

namespace common\models;

use common\helpers\ErrorCode;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="Pagination")
 * )
 */
class Pagination extends Model
{
    /**
     * @SWG\Property(property="totalCount", type="integer"),
     * @SWG\Property(property="pageCount", type="integer"),
     * @SWG\Property(property="currentPage", type="integer"),
     * @SWG\Property(property="pageSize", type="integer"),
     */
}
