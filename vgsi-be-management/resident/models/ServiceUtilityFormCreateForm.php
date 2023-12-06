<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ServiceUtilityForm;
use resident\models\ServiceUtilityFormResponse;
use common\models\ApartmentMapResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityFormCreateForm")
 * )
 */
class ServiceUtilityFormCreateForm extends Model
{
    /**
     * @SWG\Property(description="Id - Bắt buộc khi update", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Title")
     * @var string
     */
    public $title;

    /**
     * @SWG\Property(property="elements", type="array",
     *      @SWG\Items(type="object",
     *          @SWG\Property(property="order", type="integer", description="Thứ tự trong form: nếu cùng order thì hiển thị trên 1 hàng ngang"),
     *          @SWG\Property(property="location", type="integer", description="Nếu cùng thứ tự trên 1 hàng xếp theo location"),
     *          @SWG\Property(property="label", type="string", description="Text hiển thị"),
     *          @SWG\Property(property="type", type="string", description="text|textarea|checkBox|radioBox|select|button|file|image|table"),
     *          @SWG\Property(property="options", type="object", description="khi type!=table",
     *              @SWG\Property(property="key", type="string"),
     *              @SWG\Property(property="value", type="string"),
     *              @SWG\Property(property="attribute", type="string", description="readonly|disabled|multiple|selected"),
     *          ),
     *          @SWG\Property(property="option_table", type="object", description="khi type=table",
     *              @SWG\Property(property="head", type="array",
     *                  @SWG\Items(type="string")
     *              ),
     *              @SWG\Property(property="body", type="array",
     *                  @SWG\Items(type="array",
     *                      @SWG\Items(type="string")
     *                  )
     *              ),
     *              @SWG\Property(property="foot", type="array",
     *                  @SWG\Items(type="string")
     *              ),
     *          ),
     *      ),
     * ),
     * @var string
     */
    public $elements;

    /**
     * @SWG\Property(description="Apartment Id")
     * @var integer
     */
    public $apartment_id;

    /**
     * @SWG\Property(description="Type: 0: đăng ký sân chơi, 2: đăng ký thang máy, 3: ...")
     * @var integer
     */
    public $type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'apartment_id'], 'required', "on" => ['update', 'delete']],
            [['title', 'apartment_id', 'elements', 'type'], 'required', "on" => ['create']],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if(empty($apartmentMapResidentUser)){
                Yii::error('apartmentMapResidentUser empty');
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $serviceUtilityForm = new ServiceUtilityForm();
            $serviceUtilityForm->load(CUtils::arrLoad($this->attributes), '');
            $serviceUtilityForm->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
            $serviceUtilityForm->building_area_id = $apartmentMapResidentUser->building_area_id;
            $serviceUtilityForm->resident_user_id = $user->id;
            if (isset($this->elements) && is_array($this->elements)) {
                $serviceUtilityForm->elements = Json::encode($this->elements);
            }
            if (!$serviceUtilityForm->save()) {
                Yii::error($serviceUtilityForm->getErrors());
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $serviceUtilityForm->getErrors()
                ];
            }
            $transaction->commit();
            $serviceUtilityForm->sendNotifyToManagementUser(null, $user, ServiceUtilityForm::CREATE);
            return ServiceUtilityFormResponse::findOne($serviceUtilityForm->id);
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
    }

    public function update()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $item = ServiceUtilityForm::findOne(['id' => (int)$this->id]);
            if ($item) {
                $item->load(CUtils::arrLoad($this->attributes), '');
                if (isset($this->elements) && is_array($this->elements)) {
                    $item->elements = Json::encode($this->elements);
                }
                if (!$item->save()) {
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                }
                $transaction->commit();
                return ServiceUtilityFormResponse::findOne($item->id);
            } else {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
    }
}
