<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\AnnouncementSurvey;
use common\models\ServiceWaterFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="SurveyAnswerResponse")
 * )
 */
class SurveyAnswerResponse extends AnnouncementSurvey
{
    /**
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="total_apartment_capacity", type="number", description="tổng diện tích căn hộ"),
     * @SWG\Property(property="total_answer", type="integer"),
     */
}
