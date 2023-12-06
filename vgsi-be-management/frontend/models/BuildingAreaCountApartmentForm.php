<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\BuildingArea;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="BuildingAreaCountApartmentForm")
 * )
 */
class BuildingAreaCountApartmentForm extends Model
{
    /**
     * @SWG\Property(property="ids", description="Ids - array id building area", type="array",
     *      @SWG\Items(default=1, type="integer")
     * )
     * @var array
     */
    public $ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ids'], 'required'],
        ];
    }

    private function getArrayId($parent_id, $id_arr){
        $areas = BuildingArea::find()->where(['parent_id' => (int)$parent_id, 'is_deleted' => BuildingArea::NOT_DELETED])->all();
        if(empty($areas)){
            return $id_arr;
        }else{
            foreach ($areas as $area){
                $id_arr[] = $area->id;
                $id_arr_child = self::getArrayId($area->id, $id_arr);
                $id_arr = array_merge($id_arr,$id_arr_child);
                $id_arr = array_unique($id_arr);
            }
            return $id_arr;
        }
    }
    public function countApartment()
    {
        if(!empty($this->ids) && is_array($this->ids)){
            $id_arr = $this->ids;
            foreach ($this->ids as $id){
                $id_arr_child = self::getArrayId($id, $id_arr);
                $id_arr = array_merge($id_arr,$id_arr_child);
                $id_arr = array_unique($id_arr);
            }
            Yii::info($id_arr);
            $arr_new = [];
            foreach ($id_arr as $k){
                $arr_new[] = $k;
            }
            return [
                'total_count' => (int)Apartment::find()->where(['in', 'building_area_id', $id_arr])->andWhere(['is_deleted' => Apartment::NOT_DELETED])->count(),
                'building_area_ids' => $arr_new
            ];
        }
        return ['total_count' => 0];
    }
}
