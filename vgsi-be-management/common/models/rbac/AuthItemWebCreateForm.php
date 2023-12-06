<?php

namespace common\models\rbac;

use common\helpers\ErrorCode;
use common\models\rbac\AuthItemWeb;
use PHPUnit\Util\Json;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AuthItemWebCreateForm")
 * )
 */
class AuthItemWebCreateForm extends Model
{
    /**
     * @SWG\Property(property="codes", description="Codes : danh sách code cần update", type="array",
     *     @SWG\Items(type="string", default="/ticket/list"),
     * ),
     * @var array
     */
    public $codes;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codes'], 'required'],
        ];
    }

    public function create(){
        if (!empty($this->codes) && is_array($this->codes)) {
            AuthItemWeb::deleteAll();
            foreach ($this->codes as $code) {
                $item = new AuthItemWeb();
                $item->code = $code;
                $item->description = $code;
                if(!$item->save()){
                    return [
                        'success' => false,
                        'message' => Yii::t('common', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                }
            }
            //update lại data web cho các auth item data web
            $authItems = AuthItem::find()->where(['type' => AuthItem::TYPE_ROLE])->andWhere(['not', ['data_web' => null]])->all();
            foreach ($authItems as $authItem){
                $dataWebNew = [];
                $dataWebs = json_decode($authItem->data_web, true);
                foreach ($dataWebs as $dataWeb){
                    if(in_array($dataWeb, $this->codes)){
                        $dataWebNew[] = $dataWeb;
                    }
                }
                if(empty($dataWebNew)){
                    $authItem->data_web = null;
                }else{
                    $authItem->data_web = json_encode($dataWebNew);
                }
                $authItem->save();
            }
            return [
                'success' => true,
            ];
        }else{
            return [
                'success' => false,
                'message' => Yii::t('common', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
    }
}
