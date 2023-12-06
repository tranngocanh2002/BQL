<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\CardManagement;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="CardManagementChangeStatusForm")
 * )
 */
class CardManagementChangeStatusForm extends Model
{
    public $id;

    public $ids;

    public $cards;

    public $type;

    public $status;
    
    public $apartment_id;

    public $resident_user_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type','status','apartment_id','resident_user_id'], 'integer'],
            [['ids', 'cards'], 'safe']
        ];
    }

    public function approved()
    {
        if(empty($this->id)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        // if(empty($this->cards) || !is_array($this->cards)){
        //     return [
        //         'success' => false,
        //         'message' => Yii::t('frontend', "Invalid data"),
        //         'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
        //     ];
        // }
        if(empty($this->apartment_id)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        if(empty($this->resident_user_id)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $buildingCluster = Yii::$app->building->BuildingCluster;
            $cardManagement = CardManagement::find()->where(['id' => $this->id, 'building_cluster_id' => $buildingCluster->id])->one();
            $cardManagement->apartment_id = $this->apartment_id;
            // resident_user_id là apartment_map_resident_user_id
            $cardManagement->resident_user_id = $this->resident_user_id;
            if(empty($cardManagement)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Incorrect status"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $cardManagement->status = $this->status;
            if(!$cardManagement->save()){
                Yii::error($cardManagement->errors);
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "System busy"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Change Status Success"),
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

    public function block()
    {
        if(empty($this->ids) || !is_array($this->ids)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $buildingCluster = Yii::$app->building->BuildingCluster;
            $cardManagements = CardManagementResponse::find()->where(['id' => $this->ids, 'building_cluster_id' => $buildingCluster->id])
                ->andWhere(['<>', 'status', CardManagement::STATUS_BLOCK])->all();
            if(empty($cardManagements)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Incorrect status"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            foreach ($cardManagements as $cardManagement){
                $cardManagement->status = CardManagement::STATUS_BLOCK;
                if(!$cardManagement->save()){
                    Yii::error($cardManagement->errors);
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "System busy"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                    ];
                }
                // $cardManagement->sendEparking();
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Change Status Success"),
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
    public function changeStatus()
    {
        // if(empty($this->cards) || !is_array($this->cards)){
        //     return [
        //         'success' => false,
        //         'message' => Yii::t('frontend', "Invalid data"),
        //         'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
        //     ];
        // }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $buildingCluster = Yii::$app->building->BuildingCluster;
            // foreach ($this->cards as $card){
            //     if(empty($card['id'])){
            //         $transaction->rollBack();
            //         return [
            //             'success' => false,
            //             'message' => Yii::t('frontend', "Invalid data"),
            //             'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            //         ];
            //     }
            //     $card_code = '';
            //     if(!empty($card['code'])){
            //         $card_code = $card['code'];
            //     }
            //     $checkCode = CardManagement::find()->where(['number' => $card_code, 'building_cluster_id' => $buildingCluster->id])
            //         ->andWhere(['<>', 'id', $card['id']])->one();
            //     if(!empty($checkCode)){
            //         $transaction->rollBack();
            //         return [
            //             'success' => false,
            //             'message' => Yii::t('frontend', "Mã thẻ đã tồn tại: " . $checkCode),
            //             'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            //         ];
            //     }
            //     $card_number = '';
            //     if(!empty($card['number'])){
            //         $card_number = $card['number'];
            //     }
            //     $checkNumber = CardManagement::find()->where(['number' => $card_number, 'building_cluster_id' => $buildingCluster->id])
            //         ->andWhere(['<>', 'id', $card['id']])->one();
            //     if(!empty($checkNumber)){
            //         $transaction->rollBack();
            //         return [
            //             'success' => false,
            //             'message' => Yii::t('frontend', "Số thẻ đã tồn tại: " . $card_number),
            //             'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            //         ];
            //     }
            //     $cardManagement = CardManagement::find()->where(['id' => $card['id'], 'building_cluster_id' => $buildingCluster->id])
            //         ->andWhere(['not', ['status' => [CardManagement::STATUS_ACTIVE]]])->one();
            //     if(empty($cardManagement)){
            //         $transaction->rollBack();
            //         return [
            //             'success' => false,
            //             'message' => Yii::t('frontend', "Incorrect status"),
            //             'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            //         ];
            //     }
            //     $cardManagement->status = CardManagement::STATUS_ACTIVE;
            //     $cardManagement->number = $card_number;
            //     $cardManagement->type = CardManagement::TYPE_TU; // mặc định là thẻ rfid
            //     if(!empty($card['type'])){
            //         $cardManagement->type = $card['type'];
            //     }
            //     if(!$cardManagement->save()){
            //         Yii::error($cardManagement->errors);
            //         $transaction->rollBack();
            //         return [
            //             'success' => false,
            //             'message' => Yii::t('frontend', "System busy"),
            //             'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            //         ];
            //     }
            //     // $cardManagement->sendEparking();
            // }
            $cardManagement = CardManagement::find()->where(['id' => $this->id, 'building_cluster_id' => $buildingCluster->id])->one();
            if(empty($cardManagement)){
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Incorrect card"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $cardManagement->status = $this->status;
            if(!$cardManagement->save()){
                Yii::error($cardManagement->errors);
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "System busy"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Cancel card success"),
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
}
