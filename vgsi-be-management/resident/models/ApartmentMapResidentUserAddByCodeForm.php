<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentMapResidentUserAddByCodeForm")
 * )
 */
class ApartmentMapResidentUserAddByCodeForm extends Model
{
    public $type;
    public $type_relationship;
    public $is_member;
    /**
     * @SWG\Property(description="Apartment Code", default="sasca", type="string")
     * @var integer
     */
    public $apartment_code;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_code'], 'required'],
            [['apartment_code'], 'string'],
            [['apartment_code'], 'exist', 'skipOnError' => true, 'targetClass' => Apartment::className(), 'targetAttribute' => ['apartment_code' => 'code'], 'message' => Yii::t('resident', 'Apartment Code Invalid')],
        ];
    }

    public function addByCode()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = Yii::$app->user->getIdentity();
            $apartment = Apartment::findOne(['code' => $this->apartment_code]);
            //map resident user vào căn hộ vừa tạo
            $this->type = ApartmentMapResidentUser::TYPE_MEMBER;
            $this->type_relationship = ApartmentMapResidentUser::TYPE_RELATIONSHIP_OTHER;
            $this->is_member = ApartmentMapResidentUser::IS_MEMBER;
            $apartmentMapResidentUser = ApartmentMapResidentUser::getOrCreate($apartment, $user, $this);
            if(!$apartmentMapResidentUser['success']){
                $transaction->rollBack();
                return $apartmentMapResidentUser;
            }
            if(!$apartmentMapResidentUser['is_new']){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('resident', "Residents were in the apartment"),
                ];
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('resident', "Add success"),
            ];
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            return [
                'success' => false,
                'message' => Yii::t('resident', "System busy"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                // 'errors' => $ex->getMessage()
            ];
        }
    }
}
