<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
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
            [['apartment_id'], 'required', "on" => ['update', 'create', 'delete']],
            [['id'], 'required', "on" => ['update', 'delete']],
            [['phone', 'email'], 'string'],
            [['apartment_id', 'resident_user_id', 'id'], 'integer'],
        ];
    }

    public function create()
    {
        //check map apartment
        $user = Yii::$app->user->identity;
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => $this->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $item = new ApartmentMapResidentUserReceiveNotifyFee();
        $item->load(CUtils::arrLoad($this->attributes), '');
        $item->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
        if (empty($item->resident_user_id) && empty($item->phone) && empty($item->email)) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        if (!$item->save()) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $item->getErrors()
            ];
        }
        return ApartmentMapResidentUserReceiveNotifyFeeResponse::findOne(['id' => $item->id]);
    }

    public function update()
    {
        //check map apartment
        $user = Yii::$app->user->identity;
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => $this->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $item = ApartmentMapResidentUserReceiveNotifyFeeResponse::findOne(['id' => (int)$this->id, 'apartment_id' => $this->apartment_id]);
        if ($item) {
            $item->load(CUtils::arrLoad($this->attributes), '');
            if (empty($item->resident_user_id) && empty($item->phone) && empty($item->email)) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            if (!$item->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
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
        if (!$this->id) {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }

        //check map apartment
        $user = Yii::$app->user->identity;
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => $this->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }

        $item = ApartmentMapResidentUserReceiveNotifyFee::findOne(['id' => $this->id, 'apartment_id' => $this->apartment_id]);
        if ($item->delete()) {
            return [
                'success' => true,
                'message' => Yii::t('resident', "Delete Success")
            ];
        } else {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
