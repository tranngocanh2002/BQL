<?php

namespace frontend\models;

use common\helpers\ApiHelper;
use common\helpers\ErrorCode;
use common\models\ResidentUserIdentification;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="IdentifiedStatusForm")
 * )
 */
class IdentifiedStatusForm extends Model
{
    /**
     * @SWG\Property(description="resident user id")
     * @var integer
     */
    public $resident_user_id;

    /**
     * @SWG\Property(description="Status:0 - mới khởi tạo, 1 - được giám sát, 2 - lỗi chưa giám sát được")
     * @var integer
     */
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resident_user_id', 'status'], 'integer']
        ];
    }

    public function event()
    {
        Yii::info($this->attributes);
        $residentUserIdentification = ResidentUserIdentification::findOne(['resident_user_id' => $this->resident_user_id]);
        if(!empty($residentUserIdentification)){
            $residentUserIdentification->status = $this->status;
            if(!$residentUserIdentification->save()){
                Yii::error($residentUserIdentification->errors);
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Error"),
                ];
            }
        }else{
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Resident User Empty"),
            ];
        }
        return [
            'success' => true,
            'message' => Yii::t('frontend', "Success"),
        ];
    }
}
