<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUserReceiveNotifyFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentMapResidentUserReceiveNotifyFeeForm")
 * )
 */
class ApartmentMapResidentUserReceiveNotifyFeeForm extends Model
{
    /**
     * @SWG\Property(description="Id - bắt buộc khi update hoạc delete", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="apartment_id - bắt buộc khi update hoạc create ", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="resident_user_id", default=1, type="integer")
     * @var integer
     */
    public $resident_user_id;

    /**
     * @SWG\Property(description="Phone")
     * @var string
     */
    public $phone;

    /**
     * @SWG\Property(description="Email")
     * @var string
     */
    public $email;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required', "on" => ['update', 'create']],
            [['id'], 'required', "on" => ['update', 'delete']],
            [['phone', 'email'], 'string'],
            [['apartment_id', 'resident_user_id', 'id'], 'integer'],
        ];
    }

    public function create()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $item = new ApartmentMapResidentUserReceiveNotifyFee();
        $item->load(CUtils::arrLoad($this->attributes), '');
        $item->building_cluster_id = $buildingCluster->id;
        if (empty($item->resident_user_id) && empty($item->phone) && empty($item->email)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        if (!$item->save()) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $item->getErrors()
            ];
        }
        return ApartmentMapResidentUserReceiveNotifyFeeResponse::findOne(['id' => $item->id]);
    }

    public function update()
    {
        $item = ApartmentMapResidentUserReceiveNotifyFeeResponse::findOne(['id' => (int)$this->id]);
        if ($item) {
            $item->load(CUtils::arrLoad($this->attributes), '');
            if (empty($item->resident_user_id) && empty($item->phone) && empty($item->email)) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            if (!$item->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
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
        if (!$this->id) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
        $item = ApartmentMapResidentUserReceiveNotifyFee::findOne($this->id);
        if ($item->delete()) {
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Delete Success")
            ];
        } else {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
