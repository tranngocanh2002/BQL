<?php

namespace frontend\models;

use common\models\ServiceUtilityForm;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceUtilityFormResponse")
 * )
 */
class ServiceUtilityFormResponse extends ServiceUtilityForm
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="title", type="string"),
     * @SWG\Property(property="resident_user_id", type="integer"),
     * @SWG\Property(property="resident_user_phone", type="string"),
     * @SWG\Property(property="resident_user_name", type="string"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="building_area_name", type="string"),
     * @SWG\Property(property="reason", type="string", description="lý do"),
     * @SWG\Property(property="type", type="integer", description="Type: 0: đăng ký sân chơi, 2: đăng ký thang máy, 3: ..."),
     * @SWG\Property(property="status", type="integer", description="Trạng thái xử lý của yêu form: -1: customer hủy, 0- khởi tạo, 1 - đồng ý, 2 - không đồng ý"),
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
     * @SWG\Property(property="management_user_id", type="integer"),
     * @SWG\Property(property="management_user_name", type="string", description="người duyệt"),
     * @SWG\Property(property="agree_time", type="integer", description="thời gian duyệt"),
     * @SWG\Property(property="created_at", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'title',
            'resident_user_id',
            'resident_user_phone' => function ($model) {
                if (!empty($model->residentUser)) {
                    return $model->residentUser->phone;
                }
                return '';
            },
            'resident_user_name' => function ($model) {
                if (!empty($model->apartmentMapResidentUser)) {
                    return $model->apartmentMapResidentUser->resident_user_first_name . ' ' . $model->apartmentMapResidentUser->resident_user_last_name;
                }
                return '';
            },
            'apartment_id',
            'apartment_name' => function($model){
                if (!empty($model->apartment)) {
                    return $model->apartment->name;
                }
                return '';
            },
            'type',
            'status',
            'elements'=> function($model){
                if (!empty($model->elements)) {
                    return Json::decode($model->elements, true);
                }
                return [];
            },
            'reason',
            'management_user_id' => function($model){
                return $model->updated_by;
            },
            'management_user_name' => function($model){
                if($model->managementUserAgree && in_array($model->status, [ServiceUtilityForm::STATUS_AGREE, ServiceUtilityForm::STATUS_DISAGREE])){
                    return $model->managementUserAgree->first_name . ' ' . $model->managementUserAgree->last_name;
                }
                return null;
            },
            'agree_time' => function($model){
                if(in_array($model->status, [ServiceUtilityForm::STATUS_AGREE, ServiceUtilityForm::STATUS_DISAGREE])){
                    return $model->updated_at;
                }
                return null;
            },
            'created_at',
        ];
    }
}
