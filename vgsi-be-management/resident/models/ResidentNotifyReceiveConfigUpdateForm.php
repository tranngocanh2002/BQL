<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentNotifyReceiveConfig;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentNotifyReceiveConfigUpdateForm")
 * )
 */
class ResidentNotifyReceiveConfigUpdateForm extends Model
{
    /**
     * @SWG\Property(description="id căn hộ", default=0, type="integer")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="0 - hủy tất cả, 1 - chọn tất cả", default=0, type="integer")
     * @var integer
     */
    public $check_all;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id', 'check_all'], 'required'],
            [['apartment_id', 'check_all'], 'integer'],
        ];
    }

    public function update()
    {
        $user = Yii::$app->user->identity;
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $user->phone, 'apartment_id' => $this->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
        $res = ResidentNotifyReceiveConfig::updateAll(
            [
                'action_create' => $this->check_all,
                'action_update' => $this->check_all,
                'action_cancel' => $this->check_all,
                'action_delete' => $this->check_all,
                'action_approved' => $this->check_all,
                'action_comment' => $this->check_all,
                'action_rate' => $this->check_all,
            ],
            ['building_cluster_id' => $building_cluster_id, 'resident_user_id' => $user->id]
        );
//        if (!$res) {
//            return [
//                'success' => false,
//                'message' => Yii::t('resident', "Update Error"),
//                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
//            ];
//        }
        return [
            'success' => true,
            'message' => Yii::t('resident', "Update Success"),
        ];
    }
}
