<?php

namespace frontend\models;

use common\models\Apartment;
use common\models\BuildingArea;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ReportCountApartmentResponseForm")
 * )
 */
class ReportCountApartmentResponseForm extends Model
{
    /**
     * @SWG\Property(property="apartment_form_type", type="array",
     *     @SWG\Items(type="object",
     *          @SWG\Property(property="form_type", type="integer"),
     *          @SWG\Property(property="form_type_name", type="string"),
     *          @SWG\Property(property="form_type_name_en", type="string"),
     *          @SWG\Property(property="total_count", type="integer"),
     *     ),
     * ),
     * @SWG\Property(property="apartment_by_building_area", type="array",
     *     @SWG\Items(type="object",
     *          @SWG\Property(property="building_area_id", type="integer"),
     *          @SWG\Property(property="building_area_name", type="string"),
     *          @SWG\Property(property="total_count", type="integer"),
     *          @SWG\Property(property="apartment_form_type", type="array",
     *              @SWG\Items(type="object",
     *                  @SWG\Property(property="form_type", type="integer"),
     *                  @SWG\Property(property="form_type_name", type="string"),
     *                  @SWG\Property(property="form_type_name_en", type="string"),
     *                  @SWG\Property(property="total_count", type="integer"),
     *              ),
     *          ),
     *     ),
     * ),
     */

    public function countApartment()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $sql = Yii::$app->db;

        $arrayDataRes = [
        ];

        //count apartment
        $apartment = [];
        $type_list = Yii::$app->params['Apartment_form_type_list'];
        $type_en_list = Yii::$app->params['Apartment_form_type_en_list'];
        $countApartmentFormTypes = $sql->createCommand("select form_type,count(*) as total from apartment where is_deleted = 0 and building_cluster_id = $building_cluster_id group by form_type")->queryAll();
        foreach ($countApartmentFormTypes as $countApartment){
//            if(in_array((int)$countApartment['form_type'], $type_list)){
                $apartment[] = [
                    'form_type' => (int)$countApartment['form_type'],
                    'form_type_name' => $type_list[$countApartment['form_type']] ?? null,
                    'form_type_name_en' => $type_en_list[$countApartment['form_type']] ?? null,
                    'total_count' => (int)$countApartment['total']
                ];
//            }
        }
        Yii::info($apartment);
        $arrayDataRes['apartment_form_type'] = $apartment;

        $apartmentByAreaData = [];
        $buildingAreas = BuildingArea::find()->where(['building_cluster_id' => $building_cluster_id, 'parent_id' => null, 'is_deleted' => BuildingArea::NOT_DELETED])->all();
        foreach ($buildingAreas as $buildingArea){
            $building_area_ids = [$buildingArea->id];
            $buildings = BuildingArea::find()->where(['building_cluster_id' => $building_cluster_id, 'parent_id' => $buildingArea->id, 'is_deleted' => BuildingArea::NOT_DELETED])->all();
            foreach ($buildings as $building){
                $building_area_ids[] = $building->id;
            }
            $apartmentFormTypes = self::countApartmentFormTypeByBuildingArea($building_cluster_id, $building_area_ids);
            $countAnnouncementTotal = Apartment::find()->where(['building_cluster_id' => $building_cluster_id, 'building_area_id' => $building_area_ids, 'is_deleted' => Apartment::NOT_DELETED])->count();
            $apartmentByAreaData[] = [
                'building_area_id' => $buildingArea->id,
                'building_area_name' => $buildingArea->name,
                'total_count' => (int)$countAnnouncementTotal,
                'apartment_form_type' => $apartmentFormTypes
            ];
        }
        $arrayDataRes['apartment_by_building_area'] = $apartmentByAreaData;
        return $arrayDataRes;
    }

    private function countApartmentFormTypeByBuildingArea($building_cluster_id, $building_area_ids){
        $sql = Yii::$app->db;
        $building_area_id_text = implode(',', $building_area_ids);
        //count apartment
        $apartment = [];
        $type_list = Yii::$app->params['Apartment_form_type_list'];
        $type_en_list = Yii::$app->params['Apartment_form_type_en_list'];
        foreach ($type_list as $k=>$v){
            $apartment[$k] = [
                'form_type' => $k,
                'form_type_name' => $v,
                'form_type_name_en' => $type_en_list[$k] ?? '',
                'total_count' => 0
            ];
        }
        $countApartmentFormTypes = $sql->createCommand("select form_type,count(*) as total from apartment where is_deleted = 0 and building_cluster_id = $building_cluster_id and building_area_id in ($building_area_id_text) group by form_type")->queryAll();
        foreach ($countApartmentFormTypes as $countApartment){
            $apartment[$countApartment['form_type']]['total_count'] = (int)$countApartment['total'];
        }
        return $apartment;
    }
}
