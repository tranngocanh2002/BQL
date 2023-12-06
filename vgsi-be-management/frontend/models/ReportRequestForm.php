<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\AnnouncementItem;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\Request;
use common\models\RequestCategory;
use common\models\RequestReportDate;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ReportRequestForm")
 * )
 */
class ReportRequestForm extends Model
{
    public $from_day;
    public $to_day;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_day', 'to_day'], 'integer'],
        ];
    }
    /**
     * @SWG\Property(property="status", type="array",
     *      @SWG\Items(type="object",
     *          @SWG\Property(property="status", type="integer"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(property="name_en", type="string"),
     *          @SWG\Property(property="color", type="string"),
     *          @SWG\Property(property="total", type="integer"),
     *      ),
     * ),
     * @SWG\Property(property="category", type="array",
     *      @SWG\Items(type="object",
     *          @SWG\Property(property="id", type="integer"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(property="name_en", type="string"),
     *          @SWG\Property(property="color", type="string"),
     *          @SWG\Property(property="total", type="integer"),
     *      ),
     * ),
     */
    public function byDay($params)
    {
        Yii::info($params);
        $this->load(CUtils::modifyParams($params),'');
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        Yii::info($this->from_day);
        Yii::info($this->to_day);
        $from_day = (!empty($this->from_day)) ? (int)$this->from_day : time();
        $to_day = (!empty($this->to_day)) ? (int)$this->to_day : time();
        Yii::info($from_day);
        Yii::info($to_day);
        Yii::info(date('Y-m-d 00:00:00', $from_day));
        Yii::info(date('Y-m-d 23:59:59', $to_day));
        $startDay = strtotime(date('Y-m-d 00:00:00', $from_day));
        $endDay = strtotime(date('Y-m-d 23:59:59', $to_day));

        $sql = Yii::$app->db;
        $countStatus = $sql->createCommand("select status,count(*) as total from request where building_cluster_id = $building_cluster_id and `created_at` >= $startDay and `created_at` <= $endDay group by status")->queryAll();
        $statusArray = [];
        foreach ($countStatus as $row){
            $statusArray[$row['status']] = [
                'status' => $row['status'],
                'name' => Request::$status_list[$row['status']] ?? $row['status'],
                'name_en' => Request::$status_en_list[$row['status']] ?? $row['status'],
                'color' => Request::$status_color[$row['status']] ?? $row['status'],
                'total' => (int)$row['total'],
            ];
        }

        $countCategory = $sql->createCommand("select request_category_id,count(*) as total from request where building_cluster_id = $building_cluster_id and `created_at` >= $startDay and `created_at` <= $endDay group by request_category_id")->queryAll();
        $categoryArray = [];
        foreach ($countCategory as $row){
            $requestCategory = RequestCategory::findOne(['id' => (int)$row['request_category_id']]);
            $categoryArray[] = [
                'id' => (int)$row['request_category_id'],
                'name' => $requestCategory->name,
                'name_en' => $requestCategory->name_en,
                'color' => $requestCategory->color,
                'total' => (int)$row['total']
            ];
        }
        return [
           'status' =>  array_values($statusArray),
           'category' =>  $categoryArray,
        ];
//        return ReportRequestResponse::find()->where(['building_cluster_id' => $building_cluster_id])->andWhere(['>=', 'date', $startDay])->andWhere(['<=', 'date', $endDay])->all();
    }
}
