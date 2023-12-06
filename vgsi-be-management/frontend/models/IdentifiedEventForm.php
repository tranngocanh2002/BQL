<?php

namespace frontend\models;

use common\helpers\ApiHelper;
use common\helpers\ErrorCode;
use common\models\ResidentUserIdentificationHistory;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="IdentifiedEventForm")
 * )
 */
class IdentifiedEventForm extends Model
{
    /**
     * @SWG\Property(description="resident user id")
     * @var integer
     */
    public $resident_user_id;

    /**
     * @SWG\Property(description="Type: 0 - nhận diện là cư dân, 1 - nhận diện người lạ")
     * @var integer
     */
    public $type;

    /**
     * @SWG\Property(description="timestamp - thời điểm nhận diện ")
     * @var integer
     */
    public $time_event;

    /**
     * @SWG\Property(description="name image ")
     * @var string
     */
    public $image_name;

    /**
     * @SWG\Property(description="link file image ")
     * @var string
     */
    public $image_uri;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resident_user_id', 'type', 'time_event'], 'integer'],
            [['image_uri', 'image_name'], 'string'],
        ];
    }

    public function event()
    {
        Yii::info($this->attributes);
        $item = new ResidentUserIdentificationHistory();
        $item->load($this->attributes, '');
        if(!empty($item->image_uri)){
            $item->image_uri = '/uploads/identified/' . $this->image_uri . '.jpg';
        }
        if (!$item->save()) {
            Yii::error($item->errors);
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Error"),
            ];
        }
        //chỉ gửi khi thông báo người lạ
        if($item->type == 1){
            $item->sendNotify();
        }
        return [
            'success' => true,
            'message' => Yii::t('frontend', "Success"),
        ];
    }
}
