<?php

namespace frontend\models;

use common\helpers\ErrorCode;
use common\models\AnnouncementTemplate;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="AnnouncementTemplateResponse")
 * )
 */
class AnnouncementTemplateResponse extends AnnouncementTemplate
{
    /**
     * @SWG\Property(property="id", type="integer"),
     * @SWG\Property(property="building_cluster_id", type="integer", description="null : template hệ thống, != null : template riêng của từng ban quản lý"),
     * @SWG\Property(property="type", type="integer", description="0 - Thông báo thường, 1 - Thông báo phí, 2 - nhắc nợ lần một, 3 - nhắc nợ lần hai, 4 - nhắc nợ lần 3 , 5 - thông báo tạo ngừng dịch vụ"),
     * @SWG\Property(property="type_name", type="string", description="0 - Thông báo thường, 1 - Thông báo phí, 2 - nhắc nợ lần một, 3 - nhắc nợ lần hai, 4 - nhắc nợ lần 3 , 5 - thông báo tạo ngừng dịch vụ"),
     * @SWG\Property(property="name", type="string", description="name"),
     * @SWG\Property(property="name_en", type="string", description="name en"),
     * @SWG\Property(property="image", type="string", description="image"),
     * @SWG\Property(property="content_email", type="string", description="Nội dung template - các key sử dụng : APARTMENT_NAME - tên căn hộ, RESIDENT_NAME - tên chủ hộ, TABLE_ALL_FEE - bảng list các loại phí, TOTAL_FEE - tổng phí"),
     * @SWG\Property(property="content_sms", type="string", description="Nội dung template - các key sử dụng : APARTMENT_NAME - tên căn hộ, RESIDENT_NAME - tên chủ hộ, TOTAL_FEE - tổng phí"),
     */
    public function fields()
    {
        return [
            'id',
            'building_cluster_id',
            'name',
            'name_en',
            'image',
            'type',
            'type_name' => function($model){
                return AnnouncementTemplate::$type_list[$model->type];
            },
            'content_email',
            'content_sms',
        ];
    }
}
