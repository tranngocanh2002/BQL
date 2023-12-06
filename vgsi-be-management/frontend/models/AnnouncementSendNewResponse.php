<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceDebt;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementSendNewResponse")
 * )
 */
class AnnouncementSendNewResponse extends ApartmentMapResidentUser
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="building_area_id", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="apartment_parent_path", type="string"),
     * @SWG\Property(property="resident_user_name", type="string", description="chủ hộ"),
     * @SWG\Property(property="email", type="string", description="email"),
     * @SWG\Property(property="phone", type="string", description="phone"),
     * @SWG\Property(property="app", type="integer", description=""),
     * @SWG\Property(property="end_debt", type="integer", description=""),
     * @SWG\Property(property="created_at", type="integer"),
     */
    public function Params(){
        $this->load(CUtils::modifyParams(Yii::$app->request->queryParams),'');
        return $this->type ; 
    }

    public function fields()
    {
        return [
            'id',
            'building_area_id',
            'apartment_id',
            'apartment_name',
            'apartment_parent_path' => function($model){
                return substr($model->apartment_parent_path, 0, strlen($model->apartment_parent_path)-1);
                ;
            },
            'resident_user_name' => function($model){
                return trim($model->resident_user_first_name .' '. $model->resident_user_last_name);
            },
            'email' => function($model){
                return $model->resident_user_email;
            },
            'phone' => function($model){
                return $model->resident_user_phone;
            },
            'app' => function($model){
                if($model->install_app){
                    return $model->install_app;
                }
                return 0;
            },
            'created_at',
            'end_debt' => function($model){
                $serviceDebt = ServiceDebt::find()->where(['building_cluster_id'=>$model->building_cluster_id,'building_area_id'=>$model->building_area_id,'apartment_id'=>$model->apartment_id, 'status' => $this->Params()])->one();
                return $serviceDebt->end_debt ?? 0;
            },
            'status'=> function($model){
                $serviceDebt = ServiceDebt::find()->where(['building_cluster_id'=>$model->building_cluster_id,'building_area_id'=>$model->building_area_id,'apartment_id'=>$model->apartment_id, 'status' => $this->Params()])->one();
                return $serviceDebt->status ?? 0;
            },
        ];
    }
}
