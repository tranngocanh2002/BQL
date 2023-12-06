<?php

namespace frontend\models;

use common\models\ApartmentMapResidentUser;
use common\models\Request;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="RequestResponse")
 * )
 */
class RequestResponse extends Request
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="title", type="string"),
     * @SWG\Property(property="title_en", type="string"),
     * @SWG\Property(property="content", type="string"),
     * @SWG\Property(property="request_category_id", type="integer"),
     * @SWG\Property(property="request_category_name", type="string"),
     * @SWG\Property(property="request_category_name_en", type="string"),
     * @SWG\Property(property="request_category_color", type="string"),
     * @SWG\Property(property="resident_user_id", type="integer"),
     * @SWG\Property(property="resident_user_phone", type="string"),
     * @SWG\Property(property="resident_user_name", type="string"),
     * @SWG\Property(property="total_answer", type="integer", description="Tổng số câu trả lời"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="building_area_name", type="string"),
     * @SWG\Property(property="rate", type="integer"),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="status", type="integer", description="Trạng thái xử lý của yêu cầu: -2 - đã hủy, -1 - chờ xử lý, 0- Mới, 1 - đang xử lý, 2 - đã xử lý, 3 - mở lại, 4 - đã đóng"),
     * @SWG\Property(property="attach", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="number", type="string", description="mã phản ánh"),
     */
    public function fields()
    {
        return [
            'id',
            'title',
            'title_en',
            'content',
            'number' => function($model){
                return '#'.str_pad($model->id, 6, '0', STR_PAD_LEFT);
            },
            'request_category_id',
            'request_category_name' => function ($model) {
                if (!empty($model->requestCategory)) {
                    return $model->requestCategory->name;
                }
                return '';
            },
            'request_category_name_en' => function ($model) {
                if (!empty($model->requestCategory)) {
                    return $model->requestCategory->name_en;
                }
                return '';
            },
            'request_category_color' => function ($model) {
                /**
                 * @var $model Request
                 */
                if (!empty($model->requestCategory)) {
                    return $model->requestCategory->color;
                }
                return '';
            },
            'resident_user_id',
            'resident_user_phone' => function ($model) {
                if (!empty($model->residentUser)) {
                    return $model->residentUser->phone;
                }
                return '';
            },
            'resident_user_name' => function ($model) {
                /**
                 * @var $model Request
                 */
                if (!empty($model->residentUser)) {
                    $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['resident_user_phone' => $model->residentUser->phone, 'apartment_id' => $model->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
                    if(!empty($apartmentMapResidentUser)){
                        return $apartmentMapResidentUser->resident_user_first_name . ' ' . $apartmentMapResidentUser->resident_user_last_name;
                    }
                }
                return '';
            },
            'total_answer',
            'apartment_id',
            'apartment_name' => function($model){
                if (!empty($model->apartment)) {
                    return $model->apartment->name;
                }
                return '';
            },
            'building_area_name' => function($model){
                if (!empty($model->apartment)) {
                    return $model->apartment->buildingArea->parent_path . $model->apartment->buildingArea->name;
                }
                return '';
            },
            'created_at',
            'updated_at',
            'rate',
            'status',
            'status_name' => function($model){
                /**
                 * @var Request $model
                 */
                return isset(Request::$status_list[$model->status])?Request::$status_list[$model->status]:"";
            },
            'status_color' => function($model){
                /**
                 * @var Request $model
                 */
                return isset(Request::$status_color[$model->status])?Request::$status_color[$model->status]:"";
            },
            'attach' => function ($model) {
                return (!empty($model->attach)) ? json_decode($model->attach) : null;
            },
        ];
    }
}
