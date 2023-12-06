<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\RequestReportDate;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ReportRequestResponse")
 * )
 */
class ReportRequestResponse extends RequestReportDate
{
    /**
     * @SWG\Property(property="id", type="integer", description="id"),
     * @SWG\Property(property="date", type="integer", description="ngay"),
     * @SWG\Property(property="status", type="integer", description="0 - chờ xử lý, 1 - đang xử lý, 2 - hoàn thành"),
     * @SWG\Property(property="request_category_id", type="integer", description="loại danh mục yêu cầu"),
     * @SWG\Property(property="request_category_name", type="integer", description="Tên danh mục"),
     * @SWG\Property(property="total", type="integer", description="Tổng yêu cầu"),
     */
    public function fields()
    {
        return [
            'id',
            'date',
            'status',
            'request_category_id',
            'request_category_name' => function($model){
                return $model->requestCategory->name;
            },
            'total'
        ];
    }
}
