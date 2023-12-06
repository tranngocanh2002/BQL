<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
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
     * @SWG\Property(description="Id cư dân chủ thẻ", default=1, type="integer")
     * @var integer
     */
    public $resident_user_id;

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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
            [['id'], 'required', "on" => ['update', 'delete']],
            [['apartment_id'], 'required', "on" => ['delete']],
            [['id', 'resident_user_id'], 'integer'],
            [['add_map_service', 'del_map_service'], 'safe'],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            //check chủ hộ
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($apartmentMapResidentUser)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            //check thanh vien
            $apartmentMapResidentUserThanhVien = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $apartmentMapResidentUser->resident_user_phone, 'building_cluster_id' => $apartmentMapResidentUser->building_cluster_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($apartmentMapResidentUserThanhVien)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            $item = new CardManagement();
            $item->load(CUtils::arrLoad($this->attributes), '');
            $item->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
            $item->status = CardManagement::STATUS_CREATE;
            if (!$item->save()) {
                $transaction->rollBack();
                Yii::error($item->getErrors());
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            if (isset($this->add_map_service) && is_array($this->add_map_service)) {
                foreach ($this->add_map_service as $map) {
                    $cardManagementMapService = new CardManagementMapServiceForm();
                    if (!empty($map['id'])) {
                        $cardManagementMapService->setScenario('update');
                    }else{
                        $cardManagementMapService->setScenario('create');
                    }
                    $cardManagementMapService->load($map, '');
                    $cardManagementMapService->building_cluster_id = $item->building_cluster_id;
                    $cardManagementMapService->card_management_id = $item->id;
                    if (!$cardManagementMapService->validate()) {
                        $transaction->rollBack();
                        Yii::error($cardManagementMapService->getErrors());
                        return [
                            'success' => false,
                            'message' => Yii::t('resident', "Dữ liệu dịch vụ thêm vào thẻ chưa đúng"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        ];
                    }
                    if (!empty($map['id'])) {
                        $cardManagementMapService->update();
                    } else {
                        $cardManagementMapService->create();
                    }
                }
            }
            $transaction->commit();
            return CardManagementResponse::findOne(['id' => $item->id]);
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => Yii::t('resident', "System busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                // 'errors' => $ex->getMessage()
            ];
        }
    }

    public function update()
    {
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $item = CardManagementResponse::findOne(['id' => (int)$this->id, 'building_cluster_id' => $apartmentMapResidentUser->building_cluster_id]);
        if ($item) {
            $item->load(CUtils::arrLoad($this->attributes), '');
            $item->status = CardManagement::STATUS_CREATE;
            if (!$item->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            if (isset($this->add_map_service) && is_array($this->add_map_service)) {
                foreach ($this->add_map_service as $map) {
                    $cardManagementMapService = new CardManagementMapServiceForm();
                    if (!empty($map['id'])) {
                        $cardManagementMapService->setScenario('update');
                    }else{
                        $cardManagementMapService->setScenario('create');
                    }
                    $cardManagementMapService->load($map, '');
                    $cardManagementMapService->building_cluster_id = $item->building_cluster_id;
                    $cardManagementMapService->card_management_id = $item->id;
                    if (!$cardManagementMapService->validate()) {
                        Yii::error($cardManagementMapService->getErrors());
                        return [
                            'success' => false,
                            'message' => Yii::t('resident', "Dữ liệu dịch vụ thêm vào thẻ chưa đúng"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        ];
                    }
                    if (!empty($map['id'])) {
                        $cardManagementMapService->update();
                    } else {
                        $cardManagementMapService->create();
                    }
                }
            }

            if (isset($this->del_map_service) && is_array($this->del_map_service)) {
                foreach ($this->del_map_service as $map_service_id) {
                    $cardManagementMapServiceDel = new CardManagementMapServiceForm();
                    $cardManagementMapServiceDel->setScenario('delete');
                    $cardManagementMapServiceDel->id = $map_service_id;
                    if (!$cardManagementMapServiceDel->validate()) {
                        Yii::error($cardManagementMapServiceDel->getErrors());
                        return [
                            'success' => false,
                            'message' => Yii::t('resident', "Invalid data"),
                            'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        ];
                    }
                    $cardManagementMapServiceDel->delete();
                }
            }

            return $item;
        } else {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function delete()
    {
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            Yii::error("apartmentMapResidentUser empty");
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $item = CardManagement::findOne(['id' => $this->id, 'building_cluster_id' => $apartmentMapResidentUser->building_cluster_id]);
        if(!empty($item) && $item->delete()){
            CardManagementMapService::deleteAll(['card_management_id' => $this->id]);
            return [
                'success' => true,
                'message' => Yii::t('resident', "Delete Success")
            ];
        }else{
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
