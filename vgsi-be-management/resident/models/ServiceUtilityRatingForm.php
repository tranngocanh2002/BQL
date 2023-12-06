<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceMapManagement;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityConfig;
use common\models\ServiceUtilityFree;
use common\models\ServiceUtilityRatting;
use common\models\ServiceWaterFee;
use common\models\ServiceUtilityBooking;
use common\models\ServiceParkingLevel;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityRatingForm")
 * )
 */
class ServiceUtilityRatingForm extends Model
{
    /**
     * @SWG\Property(description="apartment id", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="service utility booking id", default=1, type="integer")
     * @var integer
     */
    public $service_utility_booking_id;

    /**
     * @SWG\Property(description="service utility free id", default=1, type="integer")
     * @var integer
     */
    public $service_utility_free_id;

    /**
     * @SWG\Property(description="star", default="", type="number")
     * @var string
     */
    public $star;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id', 'service_utility_booking_id', 'service_utility_free_id', 'star'], 'required'],
            [['apartment_id', 'service_utility_free_id', 'service_utility_booking_id'], 'integer'],
            [['star'], 'safe'],
        ];
    }

    public function rating()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($apartmentMapResidentUser)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $ServiceUtilityBooking = ServiceUtilityBooking::findOne(['id' => $this->service_utility_booking_id, 'apartment_id' => $apartmentMapResidentUser->apartment_id, 'created_by' => $user->id]);
            if (empty($ServiceUtilityBooking)) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $item = new ServiceUtilityRatting();
            $item->load(CUtils::arrLoad($this->attributes), '');
            $item->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
            $item->resident_user_id = $user->id;
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
            $transaction->commit();
            return ServiceUtilityRatingResponse::findOne(['id' => $item->id]);
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => Yii::t('resident', "System busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
    }

}
