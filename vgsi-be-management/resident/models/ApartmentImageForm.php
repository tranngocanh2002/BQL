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
 *   @SWG\Xml(name="ApartmentImageForm")
 * )
 */
class ApartmentImageForm extends Model
{
    /**
     * @SWG\Property(description="Apartment Id - Bắt buộc khi update", default=1, type="integer")
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
            [['apartment_id'], 'required', "on" => ['update']],
            [['medias'], 'safe'],
        ];
    }

    public function update()
    {
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(!empty($apartmentMapResidentUser)){
            $item = Apartment::findOne(['id' => (int)$this->apartment_id]);
            if ($item) {
                $item->load(CUtils::arrLoad($this->attributes), '');
                if (isset($this->medias) && is_array($this->medias)) {
                    $item->medias = !empty($this->medias) ? json_encode($this->medias) : null;
                }
                if (!$item->save()) {
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                } else {
                    return ApartmentMapResidentUserResponse::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone]);
                }
            } else {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
        }else{
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
