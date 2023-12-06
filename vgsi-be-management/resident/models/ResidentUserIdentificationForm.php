<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUserIdentification;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserIdentificationForm")
 * )
 */
class ResidentUserIdentificationForm extends Model
{
    /**
     * @SWG\Property(description="apartment id")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $medias;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
            [['apartment_id'], 'integer'],
            [['medias'], 'safe'],
        ];
    }

    public function update(){
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $item = ResidentUserIdentification::findOne(['resident_user_id' => $user->id, 'building_cluster_id' => $apartmentMapResidentUser->building_cluster_id]);
        if(empty($item)){
            $item = new ResidentUserIdentification();
            $item->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
            $item->resident_user_id = $user->id;
        }
        $item->load(CUtils::arrLoad($this->attributes),'');
        $item->is_sync = ResidentUserIdentification::IS_SYNC;
        $item->status = ResidentUserIdentification::STATUS_INACTIVE;
        if (isset($this->medias) && is_array($this->medias)) {
            $item->medias = !empty($this->medias) ? json_encode($this->medias) : null;
        }
        if(!$item->save()){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $item->getErrors()
            ];
        }
        $item->sendFaceRecognition();
        return ResidentUserIdentificationResponse::findOne(['id' => $item->id]);
    }
}
