<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserUpdateForm")
 * )
 */
class ResidentUserUpdateForm extends Model
{

    /**
     * @SWG\Property(description="first_name")
     * @var string
     */
    public $first_name;

    /**
     * @SWG\Property(description="last_name")
     * @var string
     */
    public $last_name;

    /**
     * @SWG\Property(description="Avatar")
     * @var string
     */
    public $avatar;

    /**
     * @SWG\Property(description="Is Send Email", default=1, type="integer", description="0 - không nhận, 1 - có nhận")
     * @var integer
     */
    public $is_send_email;

    /**
     * @SWG\Property(description="Is Send Notify", default=1, type="integer", description="0 - không nhận, 1 - có nhận")
     * @var integer
     */
    public $is_send_notify;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
//            [['display_name', 'email', 'first_name', 'last_name', 'avatar', 'cmtnd', 'noi_cap_cmtnd', 'nationality', 'work', 'so_thi_thuc'], 'string'],
            [['first_name', 'last_name', 'avatar'], 'string'],
            [['is_send_email', 'is_send_notify'], 'integer'],
//            [['gender', 'birthday', 'is_send_email', 'is_send_notify', 'ngay_cap_cmtnd', 'ngay_dang_ky_nhap_khau', 'ngay_dang_ky_tam_chu', 'ngay_het_han_thi_thuc'], 'integer'],
        ];
    }

    public function update(){
        $user = Yii::$app->user->getIdentity();
        $item = ResidentUserResponse::findOne(['id' => $user->id]);
        if($item){
            $item->load(CUtils::arrLoad($this->attributes), '');
            if(!$item->save()){
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            }else{
//                $item->updateApartmentMap();
                return $item;
            }
        }else{
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
