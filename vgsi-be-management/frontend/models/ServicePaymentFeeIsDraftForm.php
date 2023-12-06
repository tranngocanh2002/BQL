<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\ServiceMapManagement;
use common\models\ServicePaymentFee;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServicePaymentFeeIsDraftForm")
 * )
 */
class ServicePaymentFeeIsDraftForm extends Model
{

    const IS_DRAFT_ALL = 1;
    const IS_UNDRAFT_ALL = 1;

    /**
     * @SWG\Property(description="service map management id")
     * @var integer
     */
    public $service_map_management_id;

    /**
     * @SWG\Property(description="is_draft_all = 1 là nháp tất cả, is_draft_all = 0 thì sẽ check theo mảng is_draft_array")
     * @var integer
     */
    public $is_draft_all;

    /**
     * @SWG\Property(description="is_draft_array : mảng các id đánh dấu là nháp", type="array",
     *      @SWG\Items(type="integer", default=0),
     * )
     * @var array
     */
    public $is_draft_array;

    /**
     * @SWG\Property(description="is_undraft_all = 1 là đánh dấu bỏ nháp tất cả, is_undraft_all = 0 thì sẽ check theo mảng is_undraft_array")
     * @var integer
     */
    public $is_undraft_all;

    /**
     * @SWG\Property(description="is_undraft_array : mảng các id đánh dấu là bỏ nháp", type="array",
     *      @SWG\Items(type="integer", default=0),
     * )
     * @var array
     */
    public $is_undraft_array;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_draft_all', 'is_undraft_all', 'service_map_management_id'], 'integer'],
            [['is_draft_array', 'is_undraft_array'], 'safe'],
        ];
    }

    public function isDraft()
    {
        try {
            $user = Yii::$app->user->getIdentity();
            $buildingCluster = Yii::$app->building->BuildingCluster;
            if ($this->is_draft_all == self::IS_DRAFT_ALL && $this->is_undraft_all == self::IS_UNDRAFT_ALL) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if (
                $this->is_draft_all != self::IS_DRAFT_ALL
                && $this->is_undraft_all != self::IS_UNDRAFT_ALL
                && (empty($this->is_draft_array) || !is_array($this->is_draft_array))
                && (empty($this->is_undraft_array) || !is_array($this->is_undraft_array))
            ) {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }

            if ($this->is_draft_all == self::IS_DRAFT_ALL) {
                ServicePaymentFee::updateAll(['is_draft' => ServicePaymentFee::IS_DRAFT, 'created_at' => time()], ['building_cluster_id' => $buildingCluster->id, 'service_map_management_id' => $this->service_map_management_id, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT]);
            } else {
                if (!empty($this->is_draft_array) && is_array($this->is_draft_array)) {
                    ServicePaymentFee::updateAll(['is_draft' => ServicePaymentFee::IS_DRAFT, 'created_at' => time()], ['id' => $this->is_draft_array, 'building_cluster_id' => $buildingCluster->id, 'service_map_management_id' => $this->service_map_management_id, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT]);
                }
            }

            if ($this->is_undraft_all == self::IS_UNDRAFT_ALL) {
                $servicePaymentFees = ServicePaymentFee::find()->where(['building_cluster_id' => $buildingCluster->id, 'service_map_management_id' => $this->service_map_management_id, 'is_draft' => ServicePaymentFee::IS_DRAFT])->all();
                ServicePaymentFee::updateAll(['is_draft' => ServicePaymentFee::IS_NOT_DRAFT, 'approved_by_id' => $user->id, 'created_at' => time()], ['building_cluster_id' => $buildingCluster->id, 'service_map_management_id' => $this->service_map_management_id, 'is_draft' => ServicePaymentFee::IS_DRAFT]);
            } else {
                if (!empty($this->is_undraft_array) && is_array($this->is_undraft_array)) {
                    $servicePaymentFees = ServicePaymentFee::find()->where(['id' => $this->is_undraft_array, 'building_cluster_id' => $buildingCluster->id, 'service_map_management_id' => $this->service_map_management_id, 'is_draft' => ServicePaymentFee::IS_DRAFT])->all();
                    ServicePaymentFee::updateAll(['is_draft' => ServicePaymentFee::IS_NOT_DRAFT, 'approved_by_id' => $user->id, 'created_at' => time()], ['id' => $this->is_undraft_array, 'building_cluster_id' => $buildingCluster->id, 'service_map_management_id' => $this->service_map_management_id, 'is_draft' => ServicePaymentFee::IS_DRAFT]);
                }
            }
            if(!empty($servicePaymentFees)){
                foreach ($servicePaymentFees as $servicePaymentFee){
                    //gửi thông báo phí tới cư dân
                    $servicePaymentFee->sendNotifyToResidentUser();
                }
            }
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Update success"),
            ];
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

}
