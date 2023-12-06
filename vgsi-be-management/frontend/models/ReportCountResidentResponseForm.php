<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ResidentUserCountByAge;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ReportCountResidentResponseForm")
 * )
 */
class ReportCountResidentResponseForm extends Model
{
    public function countResident()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        return ResidentUserCountByAgeResponse::find()->where(['building_cluster_id' => $buildingCluster->id])->all();
    }

}
