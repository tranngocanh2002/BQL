<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingArea;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="BuildingAreaCreateForm")
 * )
 */
class BuildingAreaCreateForm extends Model
{
    /**
     * @SWG\Property(description="Id", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Name")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="Description")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $medias;

    /**
     * @SWG\Property(description="Status", default=1, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="Parent Id", default=1, type="integer")
     * @var integer
     */
    public $parent_id;

    /**
     * @SWG\Property(description="Short Name")
     * @var string
     */
    public $short_name;

    /**
     * @SWG\Property(description="Type", default=0, type="integer", description="0 - Tầng, 1 - Tòa nhà")
     * @var integer
     */
    public $type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_name'], 'required'],
            [['name', 'description', 'short_name'], 'string'],
            [['parent_id', 'status', 'type'], 'integer'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => BuildingArea::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['medias'], 'safe'],
            [['id'], 'required', "on" => ['update']],
            [['id'], 'integer', "on" => ['update']],
        ];
    }

    public function create()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $item = new BuildingArea();
        $item->load(CUtils::arrLoad($this->attributes), '');
        if (isset($this->medias) && is_array($this->medias)) {
            $item->medias = !empty($this->medias) ? json_encode($this->medias) : null;
        }
        $item->name = trim($item->name);
        $item->short_name = trim($item->short_name);
        $item->building_cluster_id = $buildingCluster->id;
        $checkName = BuildingArea::findOne(['name' => $item->name, 'building_cluster_id' => $buildingCluster->id, 'parent_id' => $item->parent_id, 'is_deleted' => BuildingArea::NOT_DELETED]);
        if(!empty($checkName)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Name exist"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $checkShortName = BuildingArea::findOne(['short_name' => $item->short_name, 'building_cluster_id' => $buildingCluster->id, 'is_deleted' => BuildingArea::NOT_DELETED]);
        if(!empty($checkShortName)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Short name exist"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $item->setParentPath();
        if (!$item->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $item->getErrors()
            ];
        } else {
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Create success"),
            ];
        }
    }

    public function update()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $item = BuildingAreaResponse::findOne(['id' => (int)$this->id]);
            if ($item) {
                $building_name_old = trim($item->name) . '/';
                $item->load(CUtils::arrLoad($this->attributes), '');
                $item->name = trim($item->name);
                $item->short_name = trim($item->short_name);
                $building_name_new = $item->name . '/';
                if (isset($this->medias) && is_array($this->medias)) {
                    $item->medias = !empty($this->medias) ? json_encode($this->medias) : null;
                }
                $checkName = BuildingArea::findOne(['name' => $item->name, 'building_cluster_id' => $buildingCluster->id, 'parent_id' => $item->parent_id, 'is_deleted' => BuildingArea::NOT_DELETED]);
                if(!empty($checkName)){
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Name exist"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
                $checkShortName = BuildingArea::findOne(['short_name' => $item->short_name, 'building_cluster_id' => $buildingCluster->id, 'is_deleted' => BuildingArea::NOT_DELETED]);
                if(!empty($checkShortName)){
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Short name exist"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
                if (!$item->save()) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                } else {
                    //update parent_path
                    if ($building_name_old != $building_name_new) {
                        $buildingAreas = BuildingArea::find()->where(['like', 'parent_path', $building_name_old])->andWhere(['is_deleted' => BuildingArea::NOT_DELETED, 'building_cluster_id' => $item->building_cluster_id])->all();
                        foreach ($buildingAreas as $buildingArea) {
                            $buildingArea->parent_path = str_replace($building_name_old, $building_name_new, $buildingArea->parent_path);
                            if (!$buildingArea->save()) {
                                return [
                                    'success' => false,
                                    'message' => Yii::t('frontend', "Invalid data"),
                                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                                    'errors' => $buildingArea->getErrors()
                                ];
                            };
                        }

                        $apartments = Apartment::find()->where(['like', 'parent_path', $building_name_old])->andWhere(['is_deleted' => Apartment::NOT_DELETED, 'building_cluster_id' => $item->building_cluster_id])->all();
                        foreach ($apartments as $apartment) {
                            $apartment->parent_path = str_replace($building_name_old, $building_name_new, $apartment->parent_path);
                            if (!$apartment->save()) {
                                return [
                                    'success' => false,
                                    'message' => Yii::t('frontend', "Invalid data"),
                                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                                    'errors' => $apartment->getErrors()
                                ];
                            };
                        }
                    }
                    $transaction->commit();
                    return $item;
                }
            } else {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
