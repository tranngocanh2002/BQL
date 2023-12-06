<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\BuildingArea;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="BuildingAreaResponse")
 * )
 */
class BuildingAreaResponse extends BuildingArea
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="name", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="status", type="integer"),
     * @SWG\Property(property="type", type="integer"),
     * @SWG\Property(property="type_name", type="string"),
     * @SWG\Property(property="parent_id", type="integer"),
     * @SWG\Property(property="parent_path", type="string"),
     * @SWG\Property(property="short_name", type="string"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'description',
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias) : null;
            },
            'status',
            'type',
            'type_name' => function($model){
                return BuildingArea::$arrType[$model->type] ?? $model->type;
            },
            'parent_id',
            'parent_path' => function ($model) {
                return trim($model->parent_path, '/');
            },
            'short_name',
            'created_at',
            'updated_at',
        ];
    }
}
