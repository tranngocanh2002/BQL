<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingArea;
use common\models\ResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentActiveForm")
 * )
 */
class ApartmentActiveForm extends Model
{
    /**
     * @SWG\Property(description="Apartment Id - Bắt buộc", default=1, type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
        ];
    }

    public function active()
    {
        $user = Yii::$app->user->getIdentity();
        ApartmentMapResidentUser::updateAll(['last_active' => ApartmentMapResidentUser::LAST_NOT_ACTIVE], ['resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        $res = ApartmentMapResidentUserResponse::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if($res){
            $res->last_active = ApartmentMapResidentUser::LAST_ACTIVE;
            if(!$res->save()){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
        }
        return $res;
    }
}
