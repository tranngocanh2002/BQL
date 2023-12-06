<?php

namespace resident\models;

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
     * @SWG\Property(property="type", type="integer", description="Type: 0: đăng ký sân chơi, 2: đăng ký thang máy, 3: ..."),
     * @SWG\Property(property="status", type="integer", description="-1: customer hủy, 0: khởi tạo, 1: đồng ý, 2: không đồng ý"),
     * @SWG\Property(property="apartment_id", type="integer"),
     * @SWG\Property(property="reason", type="string", description="lý do từ chối"),
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
     * @SWG\Property(property="created_at", type="integer", description="ngày tạo"),
     */
    public function fields()
    {
        return [
            'id',
            'title',
            'type',
            'status',
            'reason',
            'apartment_id',
            'elements' => function($model){
                if(!empty($model->elements)){
                    return Json::decode($model->elements, true);
                }
                return [];
            },
            'created_at'
        ];
    }
}
