<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\CardManagement;
use common\models\CardManagementMapService;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="CardManagementForm")
 * )
 */
class CardManagementForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Id căn hộ", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;


    /**
     * @SWG\Property(description="Loại thẻ: 1 - thử từ, 2 - thử rfid", default=1, type="integer")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="Id cư dân chủ thẻ", default=1, type="integer")
     * @var integer
     */
    public $resident_user_id;

    /**
     * @SWG\Property(description="Number - số thẻ")
     * @var string
     */
    public $number;

    /**
     * @SWG\Property(description="Status - 0 : chưa kích hoạt, 1 - đã kích hoạt, 2- đã hủy (các dịch vụ đi kèm đều bị khóa)")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(property="add_map_service", type="array",
     *     @SWG\Items(type="object", ref="#/definitions/CardManagementMapServiceForm"),
     * ),
     * @var array
     */
    public $add_map_service;

    /**
     * @SWG\Property(property="del_map_service", type="array",
     *     @SWG\Items(type="integer", description="map service id"),
     * ),
     * @var array
     */
    public $del_map_service;

    /**
     * @SWG\Property(description="Mã thẻ")
     * @var string
     */
    public $code;

    
    /**
     * @SWG\Property(description="description - mô tả")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(description="description_en - mô tả tiếng anh")
     * @var string
     */
    public $description_en;

    /**
     * @SWG\Property(description="reason - lý do")
     * @var string
     */
    public $reason;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['apartment_id'], 'required'],
            [['id'], 'required', "on" => ['update', 'delete']],
            [['type', 'id', 'status', 'resident_user_id'], 'integer'],
            [['number'], 'string'],
            [['code'], 'string'],
            [['add_map_service', 'del_map_service'], 'safe'],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $buildingCluster = Yii::$app->building->BuildingCluster;
            $this->number = trim($this->number);
            $this->code   = trim($this->code);
            if(!empty($this->code)){
                $checkCode = CardManagement::findOne(['code' => $this->code, 'building_cluster_id' => $buildingCluster->id]);
                if(!empty($checkCode)){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Mã thẻ đã tồn tại"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }
            if(!empty($this->number)){
                $checkNumber = CardManagement::findOne(['number' => $this->number, 'building_cluster_id' => $buildingCluster->id]);
                if(!empty($checkNumber)){
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Số thẻ đã tồn tại"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }
            $item = new CardManagement();
            $item->load(CUtils::arrLoad($this->attributes), '');
            $item->building_cluster_id = $buildingCluster->id;
            $item->number = $this->number;
            $item->code = $this->code;
            if (!$item->save()) {
                $transaction->rollBack();
                Yii::error($item->getErrors());
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            // if (isset($this->add_map_service) && is_array($this->add_map_service)) {
            //     foreach ($this->add_map_service as $map) {
            //         $cardManagementMapService = new CardManagementMapServiceForm();
            //         if (!empty($map['id'])) {
            //             $cardManagementMapService->setScenario('update');
            //         }else{
            //             $cardManagementMapService->setScenario('create');
            //         }
            //         $cardManagementMapService->load($map, '');
            //         $cardManagementMapService->building_cluster_id = $item->building_cluster_id;
            //         $cardManagementMapService->card_management_id = $item->id;
            //         if (!$cardManagementMapService->validate()) {
            //             $transaction->rollBack();
            //             Yii::error($cardManagementMapService->getErrors());
            //             return [
            //                 'success' => false,
            //                 'message' => Yii::t('frontend', "Dữ liệu dịch vụ thêm vào thẻ chưa đúng"),
            //                 'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            //             ];
            //         }
            //         if(!empty($cardManagementMapService)){
            //             if (!empty($map['id'])) {
            //                 $cardManagementMapService->update();
            //             } else {
            //                 $cardManagementMapService->create();
            //             }
            //         }
            //     }
            // }
            $transaction->commit();
            return CardManagementResponse::findOne(['id' => $item->id]);
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

    public function update()
    {
        $item = CardManagementResponse::findOne(['id' => (int)$this->id]);
        if ($item) {
            $item->load(CUtils::arrLoad($this->attributes), '');
            $item->number = trim($this->number);
            $item->code = trim($this->code);
            if(!empty($item->code)){
                $checkCode = CardManagement::find()->where(['code' => $item->code, 'building_cluster_id' => $item->building_cluster_id])
                    ->andWhere(['<>', 'id', $item->id])->one();
                if(!empty($checkCode)){
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Mã thẻ đã tồn tại"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }
            if(!empty($item->number)){
                $checkNumber = CardManagement::find()->where(['number' => $item->number, 'building_cluster_id' => $item->building_cluster_id])
                    ->andWhere(['<>', 'id', $item->id])->one();
                if(!empty($checkNumber)){
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Số thẻ đã tồn tại"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }
            }
            if (!$item->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            // if (isset($this->add_map_service) && is_array($this->add_map_service)) {
            //     foreach ($this->add_map_service as $map) {
            //         $cardManagementMapService = new CardManagementMapServiceForm();
            //         if (!empty($map['id'])) {
            //             $cardManagementMapService->setScenario('update');
            //         }else{
            //             $cardManagementMapService->setScenario('create');
            //         }
            //         $cardManagementMapService->load($map, '');
            //         $cardManagementMapService->building_cluster_id = $item->building_cluster_id;
            //         $cardManagementMapService->card_management_id = $item->id;
            //         if (!$cardManagementMapService->validate()) {
            //             Yii::error($cardManagementMapService->getErrors());
            //             return [
            //                 'success' => false,
            //                 'message' => Yii::t('frontend', "Dữ liệu dịch vụ thêm vào thẻ chưa đúng"),
            //                 'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            //             ];
            //         }
            //         if(!empty($cardManagementMapService)){
            //             if (!empty($map['id'])) {
            //                 $cardManagementMapService->update();
            //             } else {
            //                 $cardManagementMapService->create();
            //             }
            //         }
            //     }
            // }

            // if (isset($this->del_map_service) && is_array($this->del_map_service)) {
            //     foreach ($this->del_map_service as $map_service_id) {
            //         $cardManagementMapServiceDel = new CardManagementMapServiceForm();
            //         $cardManagementMapServiceDel->setScenario('delete');
            //         $cardManagementMapServiceDel->id = $map_service_id;
            //         if (!$cardManagementMapServiceDel->validate()) {
            //             Yii::error($cardManagementMapServiceDel->getErrors());
            //             return [
            //                 'success' => false,
            //                 'message' => Yii::t('frontend', "Invalid data"),
            //                 'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            //             ];
            //         }
            //         if(!empty($cardManagementMapServiceDel)){
            //             $cardManagementMapServiceDel->delete();
            //         }
            //     }
            // }
            // $item->sendEparking();
            return $item;
        } else {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function delete()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $item = CardManagement::findOne(['id' => $this->id, 'building_cluster_id' => $buildingCluster->id]);
//        $item->sendEparking(true);
        if(!empty($item) && $item->delete()){
            CardManagementMapService::deleteAll(['card_management_id' => $this->id]);
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Delete Success")
            ];
        }else{
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
