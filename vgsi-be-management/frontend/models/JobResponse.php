<?php

namespace frontend\models;

use common\models\Job;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="JobResponse")
 * )
 */
class JobResponse extends Job
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="title", type="string"),
     * @SWG\Property(property="description", type="string"),
     * @SWG\Property(property="performers", type="array", @SWG\Items(type="object", ref="#/definitions/PerformerResponse"), description="người xử lý"),
     * @SWG\Property(property="people_involveds", type="array", @SWG\Items(type="object", ref="#/definitions/PeopleInvolvedResponse"), description="người liên quan"),
     * @SWG\Property(property="assignor", type="object", ref="#/definitions/AssignorResponse", description="người giao việc"),
     * @SWG\Property(property="status", type="integer", description="-1: hủy, 0 : mới tạo, 1: đang làm, 2: đã xong"),
     * @SWG\Property(property="category", type="integer", description="0 - công việc của tôi, 1- công việc giao tôi giao, 2 - công việc liên quan"),
     * @SWG\Property(property="prioritize", type="integer", description="0 : không ưu tiên, 1: ưu tiên"),
     * @SWG\Property(property="time_start", type="integer", description="thời gian bắt đầu"),
     * @SWG\Property(property="time_end", type="integer", description="thời gian kết thúc"),
     * @SWG\Property(property="count_expire", type="integer", description="< 0 số ngày bị chậm, = 0 hôm nay đến hạn, > 0 số ngày còn lại, null công việc không có thời hạn"),
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="created_at", type="integer"),
     * @SWG\Property(property="updated_at", type="integer"),
     * @SWG\Property(property="updated_at_by_user", type="integer"),
     */
    public function fields()
    {
        return [
            'id',
            'title',
            'description',
            'performers' => function($model){
                if(!empty($model->performer)){
                    $ids = explode(',', $model->performer);
                    return PerformerResponse::find()->where(['id' => $ids])->all();
                }
                return null;
            },
            'people_involveds' => function($model){
                if(!empty($model->people_involved)){
                    $ids = explode(',', $model->people_involved);
                    return PeopleInvolvedResponse::find()->where(['id' => $ids])->all();
                }
                return null;
            },
            'assignor' => function($model){
                return AssignorResponse::findOne(['id' => $model->created_by]);
            },
            'medias' => function ($model) {
                return (!empty($model->medias)) ? json_decode($model->medias) : null;
            },
            'category' => function ($model) {
                return $model->categoryType();
            },
            'status',
            'prioritize',
            'time_start' => function($model){
                if(empty($model->time_start)){
                    return null;
                }
                return $model->time_start;
            },
            'time_end' => function($model){
                if(empty($model->time_end) || ($model->time_end > time() + 2*365*24*60*60)){
                    return null;
                }
                return $model->time_end;
            },
            'count_expire' => function($model){
                if($model->count_expire >= 9999){
                    return null;
                }
                return $model->count_expire;
            },
            'created_at',
            'updated_at',
            'updated_at_by_user',
        ];
    }
}
