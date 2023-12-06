<?php

namespace resident\models;

use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingCluster;
use common\models\ServiceMapManagement;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;
use Da\QrCode\QrCode;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentMapResidentUserResponse")
 * )
 */
class ApartmentMapResidentUserResponse extends ApartmentMapResidentUser
{
    /**
     * @SWG\Property(property="apartment_id", type="integer", description="Id căn hộ"),
     * @SWG\Property(property="apartment_name", type="string", description="Tên căn hộ"),
     * @SWG\Property(property="apartment_code", type="string", description="Mã căn hộ"),
     * @SWG\Property(property="apartment_capacity", type="string", description="Diện tích căn hộ"),
     * @SWG\Property(property="apartment_parent_path", type="string", description="Thông tin cụm và tòa nhà"),
     * @SWG\Property(property="apartment_parent_medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @SWG\Property(property="building_cluster", type="object",
     *     @SWG\Property(property="id", type="integer"),
     *     @SWG\Property(property="name", type="string"),
     *     @SWG\Property(property="domain", type="string"),
     * ),
     * @SWG\Property(property="apartment_qrcode", type="string"),
     * @SWG\Property(property="type", type="integer", description="0 - Gia đình chủ hộ, 1 - chủ hộ, 2 - khách thuê, 3 - Gia đình khách thuê"),
     * @SWG\Property(property="last_active", type="integer", description="0 - ko active, 1 - đang active"),
     * @SWG\Property(property="type_relationship", type="integer", description="Quan hệ với chủ hộ: 0 - Chủ hộ, 1 - Ông/Bà, 2 - Bố/Mẹ, 3 - Vợ/Chồng, 4 - Con, 5 - Anh/chị/em, 6 - Cháu, 7 - Khác"),
     * @SWG\Property(property="type_relationship_name", type="string"),
     * @SWG\Property(property="type_relationship_name_en", type="string"),
     * @SWG\Property(property="resident_user_map", type="array",
     *     @SWG\Items(type="object", ref="#/definitions/ApartmentMapResidentUserFullResponse")
     * ),
     * @SWG\Property(property="service_map_management", type="array",
     *     @SWG\Items(type="object", ref="#/definitions/ServiceMapManagementResponse")
     * ),
     */

    public function fields()
    {
        return [
            'type',
            'type_relationship',
            'type_relationship_name' => function($model){
                return ApartmentMapResidentUser::$type_relationship_list[$model->type_relationship] ?? null;
            },
            'type_relationship_name_en' => function($model){
                return ApartmentMapResidentUser::$type_relationship_en_list[$model->type_relationship] ?? null;
            },
            'apartment_id',
            'apartment_name',
            'apartment_code',
            'apartment_capacity',
            'apartment_parent_path' => function($model){
                return trim($model->apartment_parent_path, '/');
            },
            'apartment_medias' => function ($model) {
                $medias = $model->apartment->medias ?? "";
                return !empty($medias) ? Json::decode($medias, true) : null;
            },
            'apartment_qrcode' => function ($model) {
                $qr = Yii::$app->qr;
                $name_logo = 'logo.png';
                if(isset(Yii::$app->params['is_nam_long']) && Yii::$app->params['is_nam_long'] == true){
                    $name_logo = 'waterpoint.png';
                }
                $imageLogo = Yii::$app->getUrlManager()->getBaseUrl() . $name_logo;
                return $qr->setText($model->apartment_code)->useLogo($imageLogo)->setLogoWidth('65')->writeDataUri();
            },
            'resident_user_map' => function ($model) {
                return ApartmentMapResidentUserFullResponse::find()->where(['apartment_id' => $model->apartment_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all();
            },
            'building_cluster' => function ($model) {
                return BuildingClusterResponse::findOne(['id' => $model->building_cluster_id]);
            },
            'service_map_management' => function ($model) {
                return ServiceMapManagementResponse::find()->where(['building_cluster_id' => $model->building_cluster_id, 'is_deleted' => ServiceMapManagement::NOT_DELETED])->all();
            },
            'last_active'
        ];
    }
}
