<?php

namespace resident\models;

use common\models\ApartmentMapResidentUser;
use common\models\ManagementUser;
use common\models\Request;
use common\models\RequestMapAuthGroup;
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
     * @SWG\Property(property="resident_user_id", type="integer"),
     * @SWG\Property(property="resident_user_phone", type="string"),
     * @SWG\Property(property="resident_user_name", type="string"),
     * @SWG\Property(property="resident_user_avatar", type="string"),
     * @SWG\Property(property="total_answer", type="integer", description="Tổng số câu trả lời"),
     * @SWG\Property(property="rate", type="integer"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="apartment_name", type="string"),
     * @SWG\Property(property="number", type="string", description="mã phản ánh"),
     * @SWG\Property(property="request_answer_last", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="status_name", type="string"),
     * @SWG\Property(property="status_color", type="string"),
     * @SWG\Property(property="status", type="integer", description="Trạng thái xử lý của yêu cầu: -2 - đã hủy, -1 - chờ xử lý,0- Chờ tiếp nhập, 1 - đang xử lý, 2 - đã xử lý xong, 3- mở lại, 4 - đã đóng"),
     * @SWG\Property(property="attach", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="management_user_auth_group", type="object",
     *      @SWG\Property(property="management_user_name", type="string"),
     *      @SWG\Property(property="management_user_avatar", type="string"),
     *      @SWG\Property(property="management_user_auth_group_name", type="string"),
     *      @SWG\Property(property="management_user_auth_group_name_en", type="string"),
     * ),
     */
    public function fields()
    {
        return [
            'id',
            'title',
            'title_en',
            'content',
            'number' => function($model){
                return $model->getCode();
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
                        return $apartmentMapResidentUser->resident_user_first_name;
                    }
                }
                return '';
            },
            'resident_user_avatar' => function ($model) {
                if (!empty($model->residentUser)) {
                    return $model->residentUser->avatar;
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
            'request_answer_last' => function($model){
                return RequestAnswerResponse::find()->where(['request_id' => $model->id, 'is_deleted' => Request::NOT_DELETED])->orderBy(['id' => SORT_DESC])->one();
            },
            'management_user_auth_group' => function ($model){
                $arr = [
                    'management_user_name' => '',
                    'management_user_avatar' => '',
                    'management_user_auth_group_name' => '',
                    'management_user_auth_group_name_en' => '',
                ];
                $requestMapAuthGroup = RequestMapAuthGroup::find()->where(['request_id' => $model->id])->orderBy(['auth_group_id' => SORT_ASC])->one();
                if(!empty($requestMapAuthGroup) && !empty($requestMapAuthGroup->authGroup)){
                    $managementUser = ManagementUser::find()->where(['building_cluster_id' => $model->building_cluster_id, 'auth_group_id' => $requestMapAuthGroup->auth_group_id, 'is_deleted' => ManagementUser::NOT_DELETED])->orderBy(['id' => SORT_ASC])->one();
                    if(!empty($managementUser)){
                        $arr = [
                            'management_user_name' => $managementUser->first_name,
                            'management_user_avatar' => $managementUser->avatar,
                            'management_user_auth_group_name' => $requestMapAuthGroup->authGroup->name,
                            'management_user_auth_group_name_en' => $requestMapAuthGroup->authGroup->name_en,
                        ];
                    }
                }
                return $arr;
            },
            'created_at',
            'status' => function($model){
                $status = $model->status;
//                if($model->status == Request::STATUS_RECEIVED){
//                    $status = Request::STATUS_INIT;
//                }else if($model->status == Request::STATUS_CANCEL){
//                    $status = Request::STATUS_CLOSE;
//                }
                return $status;
            },
            'status_name' => function($model){
                $status = $model->status;
//                if($model->status == Request::STATUS_RECEIVED){
//                    $status = Request::STATUS_INIT;
//                }else if($model->status == Request::STATUS_CANCEL){
//                    $status = Request::STATUS_CLOSE;
//                }
                /**
                 * @var Request $model
                 */
                return isset(Request::$status_list[$status])?Request::$status_list[$status]:"";
            },
            'status_color' => function($model){
                $status = $model->status;
//                if($model->status == Request::STATUS_RECEIVED){
//                    $status = Request::STATUS_INIT;
//                }else if($model->status == Request::STATUS_CANCEL){
//                    $status = Request::STATUS_CLOSE;
//                }
                /**
                 * @var Request $model
                 */
                return isset(Request::$status_color[$status])?Request::$status_color[$status]:"";
            },
            'rate',
            'attach' => function ($model) {
                return (!empty($model->attach)) ? json_decode($model->attach) : null;
            },
        ];
    }
}
