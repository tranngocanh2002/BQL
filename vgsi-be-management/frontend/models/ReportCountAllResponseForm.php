<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\AnnouncementCampaign;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\Request;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityForm;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ReportCountAllResponseForm")
 * )
 */
class ReportCountAllResponseForm extends Model
{
    /**
     * @SWG\Property(property="apartment", type="object",
     *     @SWG\Property(property="status_empty", type="integer", description="Chưa ở"),
     *     @SWG\Property(property="status_live", type="integer", description="Đang ở"),
     *     @SWG\Property(property="status_handed", type="integer", description="Đã bàn giao"),
     *     @SWG\Property(property="status_not_handed", type="integer", description="Chưa bàn giao"),
     * ),
     * @SWG\Property(property="resident", type="object",
     *     @SWG\Property(property="install_app", type="integer", description="đã cài app"),
     *     @SWG\Property(property="not_install_app", type="integer", description="Chưa cài app"),
     * ),
     * @SWG\Property(property="request", type="object",
     *     @SWG\Property(property="status_init", type="integer", description="chưa xủ lý"),
     *     @SWG\Property(property="status_processing", type="integer", description="đang xử lý"),
     *     @SWG\Property(property="status_complete", type="integer", description="hoàn thành"),
     *     @SWG\Property(property="total_in_day", type="integer", description="số yêu cầu trong ngày"),
     * ),
     * @SWG\Property(property="announcement", type="object",
     *     @SWG\Property(property="total", type="integer", description="Tông thông báo"),
     *     @SWG\Property(property="total_normal", type="integer", description="Tông thông báo thường"),
     *     @SWG\Property(property="total_in_day", type="integer", description="tổng trong ngày"),
     *     @SWG\Property(property="recent_days", type="object",
     *          @SWG\Property(property="day_1", type="integer", description="Tông của ngày day_1"),
     *     ),
     * ),
     * @SWG\Property(property="service_booking", type="object",
     *     @SWG\Property(property="total_all", type="integer", description="Tổng book đang có trên hệ thống"),
     *     @SWG\Property(property="total_pending", type="integer", description="Tổng book đang chờ duyệt"),
     *     @SWG\Property(property="total_success", type="integer", description="Tổng book thành công"),
     *     @SWG\Property(property="total_in_day", type="integer", description="Tổng book trong ngày"),
     * ),
     * @SWG\Property(property="service_utility_form", type="object",
     *     @SWG\Property(property="status_agree", type="integer", description="Tổng biểu mẫu có trạng thái xác nhận"),
     *     @SWG\Property(property="status_other", type="integer", description="Tổng biểu mẫu các trạng thái khác"),
     *     @SWG\Property(property="total_in_day", type="integer", description="Tổng biểu mẫu đăng ký trong ngày"),
     * ),
     */

    public function countAll()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $sql = Yii::$app->db;

        $startDay = strtotime(date('Y-m-d 00:00:00', time()));
        $endDay = strtotime(date('Y-m-d 23:59:59', time()));

        $arrayDataRes = [
        ];
        //count apartment
        $apartment = [
            'status_empty' => 0,
            'status_live' => 0,
            'status_not_handed' => Apartment::find()->where(['OR', ['date_delivery' => null], ['date_delivery' => '']])->andWhere(['is_deleted'=>Apartment::NOT_DELETED ])->count(),
            'status_handed' => Apartment::find()->where(['NOT', ['date_delivery' => null]])->andWhere(['is_deleted'=>Apartment::NOT_DELETED ])->count(),
        ];
        $countApartments = $sql->createCommand("select status,count(*) as total from apartment where is_deleted = 0 and building_cluster_id = $building_cluster_id group by status")->queryAll();
        foreach ($countApartments as $countApartment){
            if($countApartment['status'] == Apartment::STATUS_EMPTY){
                $apartment['status_empty'] = (int)$countApartment['total'];
            }else{
                $apartment['status_live'] = (int)$countApartment['total'];
            }
        }
        $arrayDataRes['apartment'] = $apartment;


        //count resident user install app
        $resident = [
            'not_install_app' => 0,
            'install_app' => 0,
        ];
        $resident['install_app'] = (int)ApartmentMapResidentUser::find()
            ->where([
                'building_cluster_id' => $building_cluster_id,
                'install_app' => ApartmentMapResidentUser::INSTALL_APP,
                // 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED
            ])
            ->groupBy('resident_user_phone')
            ->count();
        $resident['not_install_app'] = (int)ApartmentMapResidentUser::find()
            ->where([
                'building_cluster_id' => $building_cluster_id,
                'install_app' => ApartmentMapResidentUser::NOT_INSTALL_APP,
                // 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED
            ])->groupBy('resident_user_phone')
            ->count();

        // foreach ($countResidentUsers as $countResidentUser){
        //     if($countResidentUser['install_app'] == ApartmentMapResidentUser::NOT_INSTALL_APP){
        //         $resident['not_install_app'] += 1;
        //     }else{
        //         $resident['install_app'] = 1;
        //     }
        // }
        $arrayDataRes['resident'] = $resident;

        //count request
        $request = [
            'status_init' => 0,
            'status_processing' => 0,
            'status_complete' => 0,
            'total_in_day' => 0,
        ];
        $countRequests = $sql->createCommand("select status,count(*) as total from request where building_cluster_id = $building_cluster_id group by status")->queryAll();
        foreach ($countRequests as $countRequest){
            if(in_array($countRequest['status'],[Request::STATUS_COMPLETE])){
                $request['status_complete'] += (int)$countRequest['total'];
            }else if($countRequest['status'] == Request::STATUS_PROCESSING){
                $request['status_processing'] += (int)$countRequest['total'];
            }
            if($countRequest['status'] == Request::STATUS_INIT){
                $request['status_init'] += (int)$countRequest['total'];
            }
        }

        $countRequestInDay = Request::find()->where(['building_cluster_id' => $building_cluster_id])->andWhere(['>', 'created_at', $startDay])->andWhere(['<', 'created_at', $endDay])->count();
        $request['total_in_day'] = (int)$countRequestInDay;
        $arrayDataRes['request'] = $request;

        //count thông báo
        $countAnnouncementTotal = AnnouncementCampaign::find()->where(['building_cluster_id' => $building_cluster_id])->count();
        $countAnnouncementTotalNormal = AnnouncementCampaign::find()->where(['building_cluster_id' => $building_cluster_id, 'type' => AnnouncementCampaign::TYPE_DEFAULT])->count();
        $countAnnouncementInDay = AnnouncementCampaign::find()->where(['>', 'created_at', $startDay])->andWhere(['<', 'created_at', $endDay])->andWhere(['building_cluster_id' => $building_cluster_id])->count();
        $announcement = [
            'total' => (int)$countAnnouncementTotal,
            'total_normal' => (int)$countAnnouncementTotalNormal,
            'total_in_day' => (int)$countAnnouncementInDay,
            'recent_days' => [],
        ];
        for($i = 0; $i<= 6; $i++){
            $j = 6 - $i;
            $startDayNext = strtotime(date('Y-m-d 00:00:00', strtotime("-$j day", $startDay)));
            $endDayNext = strtotime(date('Y-m-d 23:59:59', strtotime("-$j day", $startDay)));
            $countAnnouncementInDayNext = AnnouncementCampaign::find()->where(['>', 'created_at', $startDayNext])->andWhere(['<', 'created_at', $endDayNext])->andWhere(['building_cluster_id' => $building_cluster_id])->count();
            $announcement['recent_days'][$startDayNext] = (int)$countAnnouncementInDayNext;
        }
        $arrayDataRes['announcement'] = $announcement;

        //count booking
        $countServiceUtilityBookingTotal = ServiceUtilityBooking::find()->where(['building_cluster_id' => $building_cluster_id])->count();
        $countServiceUtilityBookingTotalPending = ServiceUtilityBooking::find()->where(['building_cluster_id' => $building_cluster_id, 'status' => ServiceUtilityBooking::STATUS_CREATE])->count();
        $countServiceUtilityBookingTotalSuccess = ServiceUtilityBooking::find()->where(['building_cluster_id' => $building_cluster_id, 'status' => ServiceUtilityBooking::STATUS_ACTIVE])->count();
        $countServiceUtilityBookingTotalInDay = ServiceUtilityBooking::find()->where(['building_cluster_id' => $building_cluster_id])->andWhere(['>=', 'created_at', $startDay])->andWhere(['<=', 'created_at', $endDay])->count();
        $arrayDataRes['service_booking'] = [
            'total_all' => (int)$countServiceUtilityBookingTotal,
            'total_pending' => (int)$countServiceUtilityBookingTotalPending,
            'total_success' => (int)$countServiceUtilityBookingTotalSuccess,
            'total_in_day' => (int)$countServiceUtilityBookingTotalInDay,
        ];

        //count service_utility_form
        $utilityForm = [
            'status_agree' => 0,
            'status_other' => 0,
            'total_in_day' => 0,
        ];
        $countUtilityForms = $sql->createCommand("select status,count(*) as total from service_utility_form where building_cluster_id = $building_cluster_id group by status")->queryAll();
        foreach ($countUtilityForms as $countUtilityForm){
            if($countUtilityForm['status'] == ServiceUtilityForm::STATUS_AGREE){
                $utilityForm['status_agree'] += (int)$countUtilityForm['total'];
            }else {
                $utilityForm['status_other'] += (int)$countUtilityForm['total'];
            }
        }

        $countFormInDay = ServiceUtilityForm::find()->where(['building_cluster_id' => $building_cluster_id])->andWhere(['>', 'created_at', $startDay])->andWhere(['<', 'created_at', $endDay])->count();
        $utilityForm['total_in_day'] = (int)$countFormInDay;
        $arrayDataRes['service_utility_form'] = $utilityForm;

        return $arrayDataRes;
    }
}
