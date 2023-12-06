<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\helpers\ErrorCode;
use common\helpers\StringUtils;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\Request;
use common\models\RequestCategory;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="RequestCreateForm")
 * )
 */
class RequestCreateForm extends Model
{
    /**
     * @SWG\Property(description="Id - Bắt buộc khi update", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Content")
     * @var string
     */
    public $content;

    /**
     * @SWG\Property(property="attach", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var string
     */
    public $attach;

    /**
     * @SWG\Property(description="Request Category Id")
     * @var integer
     */
    public $request_category_id;

    /**
     * @SWG\Property(description="Apartment Id")
     * @var integer
     */
    public $apartment_id;

    public $rate;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content', 'request_category_id', 'apartment_id'], 'required'],
            [['content'], 'string'],
            [['attach'], 'safe'],
            [['apartment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Apartment::className(), 'targetAttribute' => ['apartment_id' => 'id']],
            [['request_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => RequestCategory::className(), 'targetAttribute' => ['request_category_id' => 'id']],
            [['id'], 'required', "on" => ['update']],
            [['id'], 'integer', "on" => ['update']],
            [['rate'], 'integer'],
        ];
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $apartment = Apartment::findOne(['id' => $this->apartment_id]);
            //check danh mục hợp lệ
            $requestCategory = RequestCategory::findOne(['building_cluster_id' => $apartment->building_cluster_id, 'id' => $this->request_category_id]);
            if(empty($requestCategory)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "request category does not exist"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $item = new Request();
            $item->load(CUtils::arrLoad($this->attributes), '');
            if (isset($this->attach) && is_array($this->attach)) {
                $item->attach = !empty($this->attach) ? json_encode($this->attach) : null;
            }
            $item->title = CVietnameseTools::truncateString($item->content, 40);
            $item->resident_user_id = $user->id;
            $item->building_cluster_id = $apartment->building_cluster_id;
            $item->building_area_id = $apartment->building_area_id;
            if (!$item->save()) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            $transaction->commit();
            $item->sendNotifyToManagementUser(null,$user,Request::CREATE);
            return RequestResponse::findOne(['id' => (int)$item->id]);
//            return [
//                'success' => true,
//                'message' => Yii::t('resident', "Create success"),
//            ];
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
        $item = RequestResponse::findOne(['id' => (int)$this->id]);
        if ($item) {
            $item->load(CUtils::arrLoad($this->attributes), '');
            if (isset($this->attach) && is_array($this->attach)) {
                $item->attach = !empty($this->attach) ? json_encode($this->attach) : null;
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

    public function reopen()
    {
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        // $item = RequestResponse::findOne(['id' => (int)$this->id, 'status' => Request::STATUS_COMPLETE, 'resident_user_id' => $user->id, 'apartment_id' => $this->apartment_id]);
        // Bỏ tìm kiếm theo trạng thái đi để update các phản ánh thì cho về chờ xử lý hết
        $item = RequestResponse::findOne(['id' => (int)$this->id, 'resident_user_id' => $user->id, 'apartment_id' => $this->apartment_id]);
        if ($item) {
            $item->status = Request::STATUS_INIT;
            if (!$item->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            $item->sendNotifyToManagementUser(null,$user,Request::UPDATE_STATUS, Request::STATUS_REOPEN);
            return [
                'success' => true,
                'message' => Yii::t('resident', "Update success"),
            ];
        } else {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function cancel()
    {
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $item = RequestResponse::findOne(['id' => (int)$this->id, 'status' => [Request::STATUS_UNACTIVE, Request::STATUS_INIT, Request::STATUS_RECEIVED, Request::STATUS_PROCESSING, Request::STATUS_REOPEN], 'resident_user_id' => $user->id, 'apartment_id' => $this->apartment_id]);
        if ($item) {
            $item->status = Request::STATUS_CANCEL;
            if (!$item->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            $item->sendNotifyToManagementUser(null,$user,Request::UPDATE_STATUS, Request::STATUS_CANCEL);
            return [
                'success' => true,
                'message' => Yii::t('resident', "Update success"),
            ];
        } else {
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function rate()
    {
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'status' => ApartmentMapResidentUser::STATUS_ACTIVE, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $item = RequestResponse::findOne(['id' => (int)$this->id, 'status' => Request::STATUS_COMPLETE, 'resident_user_id' => $user->id, 'apartment_id' => $this->apartment_id]);
        if ($item) {
            $item->status = Request::STATUS_CLOSE;
            $item->rate = $this->rate;
            if (!$item->save()) {
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }
            $item->sendNotifyToManagementUser(null,$user,Request::UPDATE_STATUS, Request::STATUS_CLOSE);
            return [
                'success' => true,
                'message' => Yii::t('resident', "Rate success"),
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
