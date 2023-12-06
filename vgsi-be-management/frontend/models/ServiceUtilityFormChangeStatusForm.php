<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityForm;
use common\models\ResidentUserNotify;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityFormChangeStatusForm")
 * )
 */
class ServiceUtilityFormChangeStatusForm extends Model
{
    /**
     * @SWG\Property(description="ServiceUtilityForm Id", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Status", default=1, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="reason", default="", type="string", description="lý do")
     * @var string
     */
    public $reason;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'required'],
            [['id', 'status'], 'integer'],
            [['reason'], 'string'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceUtilityForm::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function changeStatus()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $request = ServiceUtilityForm::findOne(['id' => $this->id, 'building_cluster_id' => $user->building_cluster_id]);
            $request->status = $this->status;
            if(!empty($this->reason)){
                $request->reason = $this->reason;
            }
            if(!$request->save()){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "System busy"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $transaction->commit();
            if (!empty($request->apartment)) {
            $request->sendNotifyToResidentUser(null, null, ServiceUtilityForm::UPDATE_STATUS);
            $textMessage   = $this->status == 1 ? "phê duyệt" : "từ chối";
            $textMessageEn = $this->status == 1 ? "has approved" : "rejected";
            $titleFormNameEn = $this->convertFormName($request->title ?? "");
                //khởi tạo log cho từng resident user
            $residentUserNotify = new ResidentUserNotify();
            $residentUserNotify->building_cluster_id = $request->building_cluster_id;
            $residentUserNotify->building_area_id    = $request->building_area_id;
            $residentUserNotify->resident_user_id    = $request->resident_user_id;
            $residentUserNotify->title               = "Ban quản lý đã ".$textMessage." yêu cầu " . $request->title ." của bạn";
            $residentUserNotify->description         = "Ban quản lý đã ".$textMessage." yêu cầu " . $request->title ." của bạn";
            $residentUserNotify->title_en            = "Management Board ".$textMessageEn." your ".$titleFormNameEn." request";
            $residentUserNotify->description_en      = "Management Board ".$textMessageEn." your ".$titleFormNameEn." request";
            $residentUserNotify->type                = ResidentUserNotify::TYPE_SERVICE_FORM;
            $residentUserNotify->is_read             = 0;
            $residentUserNotify->is_hidden           = 0;
            $residentUserNotify->apartment_id        = $request->apartment_id;
            $residentUserNotify->service_utility_form_id = $this->id;
            $residentUserNotify->created_at          = date('dmY');
            $residentUserNotify->updated_at          = date('dmY');
            if (!$residentUserNotify->save()) {
                Yii::error($residentUserNotify->getErrors());
            }
            //end log
            }
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Change status success"),
            ];
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
    public function convertFormName($title = null)
    {
        $titleFormNameEn = "";
        switch($title)
        {
            case "Đăng ký gửi xe": $titleFormNameEn = "Resgister Parking Card"; break;
            case "Đăng ký thẻ cư dân": $titleFormNameEn = "Registration of Resident Card"; break;
            case "Đăng ký thẻ ra vào": $titleFormNameEn = "Registration of Access Card"; break;
            default : $titleFormNameEn = "Register Delivery"; 
            break;
        }
        return $titleFormNameEn ;
    }
}
