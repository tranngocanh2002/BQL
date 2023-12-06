<?php

namespace console\controllers;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\helpers\MyDatetime;
use common\helpers\QueueLib;
use common\models\ActionLog;
use common\models\AnnouncementCampaign;
use common\models\AnnouncementCategory;
use common\models\AnnouncementItem;
use common\models\AnnouncementItemSend;
use common\models\AnnouncementTemplate;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ApartmentMapResidentUserReceiveNotifyFee;
use common\models\BuildingArea;
use common\models\BuildingCluster;
use common\models\CardManagement;
use common\models\CardManagementMapService;
use common\models\HistoryResidentMapApartment;
use common\models\ManagementUser;
use common\models\ManagementUserAccessToken;
use common\models\ManagementUserDeviceToken;
use common\models\ManagementUserNotify;
use common\models\NotifySendConfig;
use common\models\PaymentConfig;
use common\models\PaymentGenCode;
use common\models\PaymentGenCodeItem;
use common\models\PaymentOrder;
use common\models\PaymentOrderItem;
use common\models\Post;
use common\models\PostCategory;
use common\models\Request;
use common\models\RequestAnswer;
use common\models\RequestAnswerInternal;
use common\models\RequestMapAuthGroup;
use common\models\RequestReportDate;
use common\models\ResidentNotifyReceiveConfig;
use common\models\ResidentUser;
use common\models\ResidentUserAccessToken;
use common\models\ResidentUserCountByAge;
use common\models\ResidentUserMapRead;
use common\models\ResidentUserNotify;
use common\models\ServiceBill;
use common\models\ServiceBillItem;
use common\models\ServiceBillNumber;
use common\models\ServiceBookingReportWeek;
use common\models\ServiceBuildingFee;
use common\models\ServiceBuildingInfo;
use common\models\ServiceDebt;
use common\models\ServiceElectricFee;
use common\models\ServiceElectricInfo;
use common\models\ServiceElectricLevel;
use common\models\ServiceFeeReportDate;
use common\models\ServiceManagementVehicle;
use common\models\ServiceMapManagement;
use common\models\ServiceOldDebitFee;
use common\models\ServiceParkingFee;
use common\models\ServiceParkingLevel;
use common\models\ServicePaymentFee;
use common\models\ServiceUtilityBooking;
use common\models\ServiceUtilityConfig;
use common\models\ServiceUtilityFree;
use common\models\ServiceUtilityPrice;
use common\models\ServiceWaterFee;
use common\models\ServiceWaterInfo;
use common\models\ServiceWaterLevel;
use Exception;
use frontend\models\RequestMapAuthGroupResponse;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use resident\models\AnnouncementItemIsHiddenForm;
use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportController extends Controller
{
    public function actionResetData($building_cluster_id, $is_all = 0){
        AnnouncementCampaign::deleteAll(['building_cluster_id' => $building_cluster_id]);
        AnnouncementItem::deleteAll(['building_cluster_id' => $building_cluster_id]);
        AnnouncementItemSend::deleteAll(['building_cluster_id' => $building_cluster_id]);
        BuildingArea::deleteAll(['building_cluster_id' => $building_cluster_id]);
        Apartment::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ApartmentMapResidentUser::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ApartmentMapResidentUserReceiveNotifyFee::deleteAll(['building_cluster_id' => $building_cluster_id]);
        CardManagement::deleteAll(['building_cluster_id' => $building_cluster_id]);
        CardManagementMapService::deleteAll(['building_cluster_id' => $building_cluster_id]);
        NotifySendConfig::deleteAll(['building_cluster_id' => $building_cluster_id]);
        PaymentGenCode::deleteAll(['building_cluster_id' => $building_cluster_id]);
        PaymentGenCodeItem::deleteAll(['building_cluster_id' => $building_cluster_id]);
        PaymentOrder::deleteAll(['building_cluster_id' => $building_cluster_id]);
        PaymentOrderItem::deleteAll(['building_cluster_id' => $building_cluster_id]);
        Post::deleteAll(['building_cluster_id' => $building_cluster_id]);
        PostCategory::deleteAll(['building_cluster_id' => $building_cluster_id]);
        RequestReportDate::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ResidentNotifyReceiveConfig::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ResidentUserCountByAge::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ResidentUserMapRead::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ResidentUserNotify::deleteAll(['building_cluster_id' => $building_cluster_id]);
        $serviceBills = ServiceBill::find()->where(['building_cluster_id' => $building_cluster_id])->all();
        foreach ($serviceBills as $serviceBill){
            ServiceBillItem::deleteAll(['service_bill_id' => $serviceBill->id]);
        }
        ServiceBill::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceBillNumber::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceBookingReportWeek::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceBuildingInfo::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceBuildingFee::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceDebt::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceElectricInfo::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceElectricFee::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceFeeReportDate::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceManagementVehicle::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceOldDebitFee::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceParkingFee::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServicePaymentFee::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceUtilityBooking::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceUtilityConfig::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceUtilityFree::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceUtilityPrice::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceWaterInfo::deleteAll(['building_cluster_id' => $building_cluster_id]);
        ServiceWaterFee::deleteAll(['building_cluster_id' => $building_cluster_id]);
        HistoryResidentMapApartment::deleteAll(['building_cluster_id' => $building_cluster_id]);
        $requests = Request::find()->where(['building_cluster_id' => $building_cluster_id])->all();
        foreach ($requests as $request){
            RequestMapAuthGroup::deleteAll(['request_id' => $request->id]);
            RequestAnswer::deleteAll(['request_id' => $request->id]);
            RequestAnswerInternal::deleteAll(['request_id' => $request->id]);
        }
        Request::deleteAll(['building_cluster_id' => $building_cluster_id]);

        if($is_all == 1){
            AnnouncementCategory::deleteAll(['building_cluster_id' => $building_cluster_id]);
            AnnouncementTemplate::deleteAll(['building_cluster_id' => $building_cluster_id]);
            ServiceWaterLevel::deleteAll(['building_cluster_id' => $building_cluster_id]);
            ServiceElectricLevel::deleteAll(['building_cluster_id' => $building_cluster_id]);
            ServiceParkingLevel::deleteAll(['building_cluster_id' => $building_cluster_id]);
            PaymentConfig::deleteAll(['building_cluster_id' => $building_cluster_id]);
            ActionLog::deleteAll(['building_cluster_id' => $building_cluster_id]);
            $managementUsers = ManagementUser::find()->where(['building_cluster_id' => $building_cluster_id])->all();
            foreach ($managementUsers as $managementUser){
                ManagementUserAccessToken::deleteAll(['management_user_id' => $managementUser->id]);
                ManagementUserDeviceToken::deleteAll(['management_user_id' => $managementUser->id]);
                ManagementUserNotify::deleteAll(['management_user_id' => $managementUser->id]);
            }
            ManagementUser::deleteAll(['building_cluster_id' => $building_cluster_id]);
            BuildingCluster::deleteAll(['id' => $building_cluster_id]);
        }
    }
    /*
     * Update căn hộ đã đăng ký định mức nước tiêu thụ
     * $spreadsheet->getSheet(0)
     * $spreadsheet->getActiveSheet()
     */
    public function actionDkdmNuoc($building_cluster_id = 7, $file_name = 'DKDM_Nuoc.xlsx')
    {
        $fxls = dirname((dirname(__DIR__))) . "/db_desgin/" . $file_name;
        $spreadsheet = IOFactory::load($fxls);
        $sheetData = $spreadsheet->getActiveSheet();
//        $xls_datas = $spreadsheet->getSheetNames();
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D'];
        while (true) {
            $rows = [];
            $stop = 0;
            foreach ($arrColumns as $col) {
                $cell = $sheetData->getCell($col . $i);
                $val = $cell->getFormattedValue();
                if ($col == 'A' && empty($val)) {
                    $stop = 1;
                    break;
                }
                if ($col == 'C' && !empty($val)) {
                    $val = $val . '/';
                    list($col1, $col2) = explode('/', $val);
                    $col1 = $col1 . '/';
                }
                if ($col == 'D') {
                    if (empty($val)) {
                        $val = 0;
                    }
                    $val = (int)$val;
                }
                $rows[] = $val;

            }
            if ($stop == 1) {
                break;
            }
//            print_r($rows);
            $i++;
            if (count($arrColumns) > count($rows)) {
                continue;
            }
            $apartment = Apartment::findOne(['building_cluster_id' => $building_cluster_id, 'name' => $rows[1], 'parent_path' => $rows[2]]);
            if (!empty($apartment)) {
                //đã tồn tại
                $apartment->set_water_level = $rows[3];
                if(!$apartment->save()){
                    Yii::error('apartment update error ' . $rows[1]);
                    echo 'apartment update error ' . $rows[1] . "\r\n";
                }
            }else{
                Yii::error('apartment empty ' . $rows[1]);
                echo 'apartment empty ' . $rows[1] . "\r\n";
            }
        }
    }


    /*
     * Update số thành viên trong căn hộ
     * $spreadsheet->getSheet(0)
     * $spreadsheet->getActiveSheet()
     */
    public function actionMemberApartment($building_cluster_id = 7, $file_name = 'import_member_apartment.xlsx')
    {
        $fxls = dirname((dirname(__DIR__))) . "/db_desgin/" . $file_name;
        $spreadsheet = IOFactory::load($fxls);
        $sheetData = $spreadsheet->getActiveSheet();
//        $xls_datas = $spreadsheet->getSheetNames();
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D'];
        while (true) {
            $rows = [];
            $stop = 0;
            foreach ($arrColumns as $col) {
                $cell = $sheetData->getCell($col . $i);
                $val = $cell->getFormattedValue();
                if ($col == 'A' && empty($val)) {
                    $stop = 1;
                    break;
                }
                if ($col == 'C' && !empty($val)) {
                    $val = $val . '/';
                    list($col1, $col2) = explode('/', $val);
                    $col1 = $col1 . '/';
                }
                if ($col == 'D') {
                    if (empty($val)) {
                        $val = 0;
                    }
                    $val = (int)$val;
                }
                $rows[] = $val;

            }
            if ($stop == 1) {
                break;
            }
//            print_r($rows);
            $i++;
            if (count($arrColumns) > count($rows)) {
                continue;
            }
            $apartment = Apartment::findOne(['building_cluster_id' => $building_cluster_id, 'name' => $rows[1], 'parent_path' => $rows[2]]);
            if (!empty($apartment)) {
                //đã tồn tại
                $apartment->total_members = $rows[3];
                if(!$apartment->save()){
                    Yii::error('apartment update error ' . $rows[1]);
                    echo 'apartment update error ' . $rows[1] . "\r\n";
                }
            }else{
                Yii::error('apartment empty ' . $rows[1]);
                echo 'apartment empty ' . $rows[1] . "\r\n";
            }
        }
    }

    public function actionDelAllCache()
    {
        $objs = [
            "app_id" => "02296a41-6be5-47dd-bf49-b4367b3b12e1",
            "subtitle" => [
                "en" => "SmartHome Notify"
            ],
            "contents" => [
                "en" => "1"
            ],
            "include_player_ids" => [
                "9bce0657-f1fa-4b49-994b-93e446162f57"
            ],
            "data" => [
                "id" => 1248287,
                "source" => 1,
                "ruleid" => "9",
                "devid" => null,
                "url" => "lumi://tabbar/tab_5/addOrEditRule"
            ]
        ];
        $queueName = 'aaaaaaa';
//        QueueLib::publishRMQ($queueName,json_encode($objs));
//        $cache = Yii::$app->cache;
//        $cache->flush();
    }

    public function actionUpdateSortName()
    {
        $buildingAreas = BuildingArea::find()->where(['parent_id' => null])->andWhere(['not', ['short_name' => null]])->all();
        foreach ($buildingAreas as $buildingArea){
            $floors = BuildingArea::find()->where(['parent_id' => $buildingArea->id])->all();
            $ids = [];
            foreach ($floors as $floor){
                $ids[] = $floor->id;
            }
            $apartments = Apartment::find()->where(['building_area_id' => $ids, 'short_name' => null])->all();
            foreach ($apartments as $apartment){
                $apartment->short_name = $buildingArea->short_name.'.'.$apartment->name;
                if($apartment->save()){
                    ApartmentMapResidentUser::updateAll(['apartment_short_name' => $apartment->short_name], ['apartment_id' => $apartment->id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
                }
            }
        }
    }

    public function actionCreateClusterDefault($file_path = null)
    {
        $buildingCluster = new \common\models\BuildingCluster();
        $buildingCluster->status = 1;
        $buildingCluster->name = 'Luci Building';
        $buildingCluster->domain = 'https://web.building.luci.vn';
        $buildingCluster->email = 'demo@gmail.com';
        $buildingCluster->hotline = '[{"title":"CSKH","phone":"0968980280"}]';
        $buildingCluster->address = 'Lê Quang Kim, Quận 8, TP. HCM';
        $buildingCluster->description = 'KHÔNG GIAN SỐNG ĐẰNG CẤP
Không làm “gián đoạn Mảng Xanh” của Bờ Sông, Bờ Hồ. Thiết lập hình ảnh mới ‘’Siluet - Bóng dáng Đô thị’, hai bên bờ sông. Thiết lập LandMark xanh ví như một biểu tượng xanh của Thành Phố. Vườn thiền trên không độc đáo.

Tổng quan
Tổng diện tích dự án: 16.600m2
Diện tích xây dựng: 3.604m2
Mật độ xây dựng: 19%
3 tháp 22 tầng gồm 2 tầng hầm, 2 tầng thương mại, 20 tầng ở (760 căn hộ)
Vườn thiền trên không 5.000m2  kết nối 2 tòa nhà
Công viên cây xanh: 8.000m2

Sau những nỗ lực không ngừng của chủ đầu tư cùng đơn vị thi công, hiện tại dự án đã chính thức cất nóc cả 4 tòa căn hộ, trong đó S1 Seasons Avenue là tòa sẽ được bàn giao sớm nhất. Đây chính là thời điểm vàng để quý khách hàng lựa chọn và đặt cọc những căn hộ đẹp nhất tại dự án, hãy đăng ký ngay với chúng tôi để nhận được nhiều ưu đãi hấp dẫn.';
        $buildingCluster->service_bill_template = '{"style_string":"\n .jsx-parser ol {\n margin: 0;\n padding: 0\n }\n \n .jsx-parser table td,\n table th {\n padding: 0\n }\n \n .jsx-parser .c7c20{\n min-width: 30%;\n color: #000000;\n font-weight: 700;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10pt;\n font-family: \"Times New Roman\";\n font-style: normal;\n text-align: right;\n }\n .jsx-parser .c7 {\n border-right-style: solid;\n padding-top: 6pt;\n border-top-width: 0pt;\n border-bottom-color: null;\n border-right-width: 0pt;\n padding-left: 0pt;\n border-left-color: null;\n padding-bottom: 6pt;\n line-height: 1.0;\n border-right-color: null;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n border-top-color: null;\n border-bottom-style: solid;\n orphans: 2;\n widows: 2;\n padding-right: 0pt;\n display: flex;\n justify-content: space-between;\n align-items: center;\n }\n \n .jsx-parser .c9 {\n border-right-style: solid;\n padding-top: 6pt;\n border-top-width: 0pt;\n border-bottom-color: null;\n border-right-width: 0pt;\n padding-left: 0pt;\n border-left-color: null;\n padding-bottom: 2pt;\n line-height: 1.0;\n border-right-color: null;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n border-top-color: null;\n border-bottom-style: solid;\n orphans: 2;\n widows: 2;\n text-align: center;\n padding-right: 0pt\n }\n .jsx-parser .c9999 {\n border-right-style: solid;\n padding-top: 1pt;\n border-top-width: 0pt;\n border-bottom-color: null;\n border-right-width: 0pt;\n padding-left: 0pt;\n border-left-color: null;\n padding-bottom: 1pt;\n line-height: 1.0;\n border-right-color: null;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n border-top-color: null;\n border-bottom-style: solid;\n orphans: 2;\n widows: 2;\n text-align: center;\n padding-right: 0pt\n }\n \n .jsx-parser .c3 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 114.8pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c26 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 86.2pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c27 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 91.5pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c15 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n background-color: #ffffff;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 68.2pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c6 {\n border-right-style: solid;\n padding: 5pt 5pt 5pt 5pt;\n border-bottom-color: #666666;\n border-top-width: 1pt;\n border-right-width: 1pt;\n border-left-color: #666666;\n vertical-align: top;\n border-right-color: #666666;\n border-left-width: 1pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 1pt;\n width: 330pt;\n border-top-color: #666666;\n border-bottom-style: solid\n }\n \n .jsx-parser .c29 {\n border-right-style: solid;\n padding: 5pt 5pt 5pt 5pt;\n border-bottom-color: #000000;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #000000;\n vertical-align: top;\n border-right-color: #000000;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 243pt;\n border-top-color: #000000;\n border-bottom-style: solid\n }\n \n .jsx-parser .c30 {\n border-right-style: solid;\n padding: 2pt 2pt 2pt 2pt;\n border-bottom-color: #dddddd;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #dddddd;\n vertical-align: top;\n border-right-color: #dddddd;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 99pt;\n border-top-color: #dddddd;\n border-bottom-style: solid\n }\n \n .jsx-parser .c19 {\n border-right-style: solid;\n padding: 5pt 5pt 5pt 5pt;\n border-bottom-color: #000000;\n border-top-width: 0pt;\n border-right-width: 0pt;\n border-left-color: #000000;\n vertical-align: top;\n border-right-color: #000000;\n border-left-width: 0pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 0pt;\n width: 255pt;\n border-top-color: #000000;\n border-bottom-style: solid\n }\n \n .jsx-parser .c14 {\n border-right-style: solid;\n padding: 5pt 5pt 5pt 5pt;\n border-bottom-color: #666666;\n border-top-width: 1pt;\n border-right-width: 1pt;\n border-left-color: #666666;\n vertical-align: top;\n border-right-color: #666666;\n border-left-width: 1pt;\n border-top-style: solid;\n border-left-style: solid;\n border-bottom-width: 1pt;\n width: 150pt;\n border-top-color: #666666;\n border-bottom-style: solid\n }\n \n .jsx-parser .c1 {\n color: #000000;\n font-weight: 400;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c25 {\n color: #000000;\n font-weight: 400;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 9pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c20 {\n color: #000000;\n font-weight: 700;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c2 {\n color: #000000;\n font-weight: 700;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10.5pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c16 {\n color: #000000;\n font-weight: 400;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 10.5pt;\n font-family: \"Times New Roman\";\n font-style: italic\n }\n \n .jsx-parser .c22 {\n color: #000000;\n font-weight: 700;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 9pt;\n font-family: \"Times New Roman\";\n font-style: normal\n }\n \n .jsx-parser .c10 {\n color: #000000;\n font-weight: 400;\n text-decoration: none;\n vertical-align: baseline;\n font-size: 11pt;\n font-family: \"Arial\";\n font-style: normal\n }\n \n .jsx-parser .c17 {\n padding-top: 0pt;\n padding-bottom: 0pt;\n line-height: 1.15;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser .c5 {\n padding-top: 0pt;\n padding-bottom: 0pt;\n line-height: 1.0;\n text-align: center\n }\n \n .jsx-parser .c0 {\n padding-top: 0pt;\n padding-bottom: 0pt;\n line-height: 1.0;\n text-align: left\n }\n \n .jsx-parser .c24 {\n width:100%;\n border-spacing: 0;\n border-collapse: collapse;\n }\n \n .jsx-parser .c18 {\n width:100%;\n border-spacing: 0;\n border-collapse: collapse;\n }\n \n .jsx-parser .c23 {\n width:100%;\n border-spacing: 0;\n border-collapse: collapse;\n }\n \n .jsx-parser .c13 {\n font-size: 16pt;\n font-family: \"Times New Roman\";\n font-weight: 700;\n }\n \n .jsx-parser .c8 {\n background-color: #ffffff;\n padding: 0pt 8pt 0pt 8pt\n }\n \n .jsx-parser .c11 {\n background-color: #ffffff;\n height: 11pt\n }\n \n .jsx-parser .c21 {\n height: 11pt\n }\n \n .jsx-parser .c4 {\n height: 24pt\n }\n \n .jsx-parser .c28 {\n background-color: #ffffff\n }\n \n .jsx-parser .c12 {\n height: 0pt\n }\n \n .jsx-parser .title {\n padding-top: 0pt;\n color: #000000;\n font-size: 26pt;\n padding-bottom: 3pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser .subtitle {\n padding-top: 0pt;\n color: #666666;\n font-size: 15pt;\n padding-bottom: 16pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser li {\n color: #000000;\n font-size: 11pt;\n font-family: \"Arial\"\n }\n \n .jsx-parser p {\n margin: 0;\n color: #000000;\n font-size: 11pt;\n font-family: \"Arial\"\n }\n \n .jsx-parser h1 {\n padding-top: 20pt;\n color: #000000;\n font-size: 20pt;\n padding-bottom: 6pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h2 {\n padding-top: 18pt;\n color: #000000;\n font-size: 16pt;\n padding-bottom: 6pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h3 {\n padding-top: 16pt;\n color: #434343;\n font-size: 14pt;\n padding-bottom: 4pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h4 {\n padding-top: 14pt;\n color: #666666;\n font-size: 12pt;\n padding-bottom: 4pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h5 {\n padding-top: 12pt;\n color: #666666;\n font-size: 11pt;\n padding-bottom: 4pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n \n .jsx-parser h6 {\n padding-top: 12pt;\n color: #666666;\n font-size: 11pt;\n padding-bottom: 4pt;\n font-family: \"Arial\";\n line-height: 1.15;\n page-break-after: avoid;\n font-style: italic;\n orphans: 2;\n widows: 2;\n text-align: left\n }\n }","jsx":"<div class=\"c8\">\r\n <p class=\"c17 c21\"><span class=\"c10\"></span></p><a id=\"t.596889bd2267afc7ef97a3091f18b552ea14f1a8\"></a><a\r\n id=\"t.0\"></a>\r\n <table class=\"c24\">\r\n <tbody>\r\n <tr class=\"c12\">\r\n <td class=\"c29\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c17\"><span class=\"c22\">Đơn vị: Luci Building</span></p>\r\n <p class=\"c17\"><span class=\"c25\">Bộ phận:.........................................</span></p>\r\n </td>\r\n <td class=\"c19\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c5\"><span class=\"c22\">Mẫu số: 01-TT</span></p>\r\n <p class=\"c5\"><span class=\"c25\">(Ban hành theo quyết định số: 48/2006/QĐ - BTC Ngày 14/9/2006 của bộ trưởng BTC)</span></p>\r\n </td>\r\n </tr>\r\n </tbody>\r\n </table>\r\n <p class=\"c7\"><span class=\"c7c20\"></span><span class=\"c13\">PHIẾU THU</span><span class=\"c7c20\">Số: {number}</span></p>\r\n <p class=\"c9 c28\"><span class=\"c2\">{execution_date}</span></p>\r\n <p class=\"c9999 c28\"><span class=\"c2\">{bill_name}</span></p>\r\n <p class=\"c9 c11\"><span class=\"c2\"></span></p><a id=\"t.e573e07dec41c32b04b625135152558b5f9d025b\"></a><a\r\n id=\"t.1\"></a>\r\n <table class=\"c23\">\r\n <tbody>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Họ và tên người nộp:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">{payer_name}</span></p>\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Căn hộ:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">{apartment_name}</span></p>\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Lý do thu:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n {fees}\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Số tiền:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c20\">{total_new_money_collected} VND</span></p>\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Viết bằng chữ:</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c20\">{total_new_money_collected_string}</span></p>\r\n </td>\r\n </tr>\r\n <tr class=\"c12\">\r\n <td class=\"c14\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Kèm theo:.................................</span></p>\r\n </td>\r\n <td class=\"c6\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c0\"><span class=\"c1\">Chứng từ gốc.</span></p>\r\n </td>\r\n </tr>\r\n </tbody>\r\n </table>\r\n <p class=\"c17 c21\"><span class=\"c10\"></span></p><a id=\"t.a888a99cd7e3fadfab7e4e8e6ed1ccd37f91d10f\"></a><a\r\n id=\"t.2\"></a>\r\n <table class=\"c18\">\r\n <tbody>\r\n <tr class=\"c4\">\r\n <td class=\"c3\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Giám đốc</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên, đóng dấu)</span>\r\n </p>\r\n </td>\r\n <td class=\"c28 c30\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Trưởng ban quản lý</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên)</span></p>\r\n </td>\r\n <td class=\"c15\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Kế toán</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên)</span></p>\r\n </td>\r\n <td class=\"c27\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Người nộp tiền</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên)</span></p>\r\n </td>\r\n <td class=\"c26\" colspan=\"1\" rowspan=\"1\">\r\n <p class=\"c9\"><span class=\"c2\">Người lập phiếu</span></p>\r\n <p class=\"c9\"><span class=\"c16\">(Ký, họ tên)</span></p>\r\n </td>\r\n </tr>\r\n </tbody>\r\n </table>\r\n <p class=\"c17 c21\"><span class=\"c10\"></span></p>\r\n</div>","jsx_row":"\n <p class=\"c0\"><span class=\"c1\">Thu {rr.service_map_management_service_name} {rr.fee_of_month}: {rr.new_money_collected} &#273;&#7891;ng</span></p>\n "}';
        $buildingCluster->city_id = 42;
        $buildingCluster->auth_item_tags = '["ADMIN","ANNOUNCE","APARTMENT","BASIC","DASHBOARD","FINANCE","MANAGEMENT-USER","REQUEST","RESIDENT","SERVICE","SERVICE_CLOUD","SETTING"]';
        $buildingCluster->limit_sms = '5000';
        $buildingCluster->sms_price = '0';
        $buildingCluster->limit_email = '5000';
        $buildingCluster->limit_notify = '50000';
        $buildingCluster->link_whether = 'https://api.openweathermap.org/data/2.5/weather?q=Ho%20Chi%20Minh,vn&APPID=66fad97f3f5ac3fe64089b2c26e41069&lang=vi&units=metric';
        if(!$buildingCluster->save()){
            var_dump($buildingCluster->errors);
            echo 'Import Error';
        }
        $buildingCluster->setDefaultData();
    }

    public function actionCreateFile($file_path = null)
    {
        $building_cluster_id = 1;
        if ($file_path == null) {
//            $file_path = '/uploads/applications/201907/'.time().'-test_nuoc.xlsx';
            $file_path = $fxls = dirname((dirname(__DIR__))) . "/db_desgin/" . time() . '-test-array.xlsx';
        }
//        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $fileandpath = $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Select');
        $apartments = ApartmentMapResidentUser::find()->where(['building_cluster_id' => $building_cluster_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all();
        $serviceParkingLevels = ServiceParkingLevel::find()->where(['building_cluster_id' => 1])->all();
        $arrLevels = '';
        foreach ($serviceParkingLevels as $serviceParkingLevel) {
            $arrLevels .= $serviceParkingLevel->code . ',';
        }
        $arrLevels = trim($arrLevels, ',');
        if (empty($arrLevels)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $i = 2;
        foreach ($apartments as $apartment) {
            $sheet->setCellValue('A' . $i, $i - 1);

            $validation = $sheet->getCell('B' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $arrLevels . '"'); // Make sure to put the list items between " and " if your list is simply a comma-separated list of values !!!

            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
    }

    public function actionUpdateResidentName()
    {
        $residentMaps = ApartmentMapResidentUser::findAll(['type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        foreach ($residentMaps as $resident) {
            $apartment = Apartment::findOne(['id' => $resident->apartment_id, 'resident_user_id' => null]);
            if (!empty($apartment)) {
                $apartment->resident_user_id = $resident->resident_user_id;
                $apartment->resident_user_name = $resident->resident_user_first_name;
                $apartment->save();
            }
        }
    }

    public function actionWaterFee($file_name = 'import_water_fee.xlsx')
    {
        $service_map_management_id = 1;
        $building_cluster_id = 1;
        $fxls = dirname((dirname(__DIR__))) . "/db_desgin/" . $file_name;
        $spreadsheet = IOFactory::load($fxls);
        $xls_datas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $i = 0;
        $is_validate = 0;
        $arrCreateError = [];
        $apartmentArrayError = [];
        $arrIndexError = [];
        foreach ($xls_datas as $xls_data) {
            $i++;
            if ($i == 1) {
                continue;
            }
            $rows = [];
            $j = 0;
            $imported = 0;
            $is_break = 0;
            foreach ($xls_data as $col) {
                if ($j == 0 && empty($col)) {
                    $is_break = 1;
                }
                if ($j == 2) {
                    $col .= '/';
                } else if ($j == 3 && !empty($col)) {
                    list($day, $month, $year) = explode('/', $col);
                    $col = $year . '-' . $month . '-' . $day;
                }
                $rows[] = $col;
                $j++;
            }
            if ($is_break == 1) {
                break;
            }
            print_r($rows);
//            return;
            $apartment = ApartmentMapResidentUser::findOne(['building_cluster_id' => $building_cluster_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'apartment_name' => $rows[1], 'apartment_parent_path' => $rows[2], 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if (empty($apartment)) {
                $apartmentArrayError[] = [
                    'line' => $i + 1,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2],
                ];
                continue;
            } else {
                continue;
                if ($is_validate == 0) {
                    //lấy ra chỉ số đầu kỳ của apartment
                    $start_index = 0;
                    $lock_time = strtotime($rows[3]);
                    $serviceWater = ServiceWaterFee::find()->where([
                        'service_map_management_id' => $service_map_management_id,
                        'building_cluster_id' => $building_cluster_id,
                        'apartment_id' => $apartment->id,
                    ])->orderBy(['end_index' => SORT_DESC])->one();
                    if (!empty($serviceWater)) {
                        $start_index = $serviceWater->end_index;
                    }
                    if ($start_index > $rows[4]) {
                        $arrIndexError[] = [
                            'line' => $i + 1,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2],
                            'start_index' => $start_index,
                            'end_index' => $rows[4],
                        ];
                    } else {
                        $serviceWaterFee = new ServiceWaterFee();
                        $serviceWaterFee->service_map_management_id = $service_map_management_id;
                        $serviceWaterFee->building_cluster_id = $building_cluster_id;
                        $serviceWaterFee->apartment_id = $apartment->id;
                        $serviceWaterFee->getTotalIndex();
                        $genchage = $serviceWaterFee->getCharge($building_cluster_id, $service_map_management_id, $rows[4], $start_index, $lock_time);
                        $serviceWaterFee->total_money = $genchage['total_money'];
                        $serviceWaterFee->description = $genchage['description'];
                        $serviceWaterFee->lock_time = $lock_time;
                        if (!$serviceWaterFee->save()) {
                            $arrCreateError[] = [
                                'line' => $i + 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $rows[2],
                            ];
                        };
                    }
                }
            }
            $imported++;
        }
        echo "imported: ";
        print_r($imported);
        echo "arrCreateError";
        print_r($arrCreateError);
        echo "apartmentArrayError";
        print_r($apartmentArrayError);
        echo "arrIndexError";
        print_r($arrIndexError);
    }

    /*
     * $spreadsheet->getSheetByName('Worksheet 1')
     * $spreadsheet->getSheet(0)
     * $spreadsheet->getActiveSheet()
     */
    public function actionDemo($file_name = 'import_demo.xlsx')
    {
        $fxls = dirname((dirname(__DIR__))) . "/db_desgin/" . $file_name;
        $spreadsheet = IOFactory::load($fxls);
        $sheetData = $spreadsheet->getActiveSheet();
//        $xls_datas = $spreadsheet->getSheetNames();
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F'];
        while (true) {
            $rows = [];
            $stop = 0;
            foreach ($arrColumns as $col) {
                $cell = $sheetData->getCell($col . $i);
                $val = $cell->getFormattedValue();
                if ($col == 'A' && empty($val)) {
                    $stop = 1;
                    break;
                }
                if($col == 'D' || $col == 'F'){
                    $format = $cell->getStyle()->getNumberFormat()->getFormatCode();
                    if($format == 'General'){
                        $format = 'd/m/Y';
                    }
                    $format = str_replace(['dd','mm','yyyy'],['d','m','Y'],$format);
                    $val = CUtils::convertStringToTimeStamp($val, $format);
                }
                $rows[] = $val;

            }
            if ($stop == 1) {
                break;
            }
            print_r($rows);
            $i++;
        }
//        $xls_datas = $spreadsheet->getActiveSheet()->toArray(null, false, false, true);
//        var_dump($xls_datas);
//        print_r($xls_datas);
        $i = 0;
//        foreach ($xls_datas as $sheetIndex => $loadedSheetName) {
//            $spreadsheet->setActiveSheetIndexByName($loadedSheetName);
//            $sheetData = $spreadsheet->getActiveSheet();
//            $highestRows = $spreadsheet->get;
//            var_dump($highestRows);
////            for ($row = 1; $row <= $highestRows; ++$row) {
////                $cell = $sheetData->getCell('D'.$row);
////                $format = $cell->getStyle()->getNumberFormat()->getFormatCode();
////                var_dump($format);
////            }
//        }
    }

    public function actionDataDemo($building_cluster_id)
    {
        $areas = ['Tòa S1', 'Tòa S2'];
        foreach ($areas as $area) {
            $buildingArea = new BuildingArea();
            $buildingArea->name = $area;
            $buildingArea->building_cluster_id = $building_cluster_id;
            $buildingArea->status = 1;
            if ($buildingArea->save()) {
                for ($i = 1; $i <= 10; $i++) {
                    $buildingFloor = new BuildingArea();
                    $buildingFloor->name = 'Tầng ' . $i;
                    $buildingFloor->building_cluster_id = $building_cluster_id;
                    $buildingFloor->parent_id = $buildingArea->id;
                    $buildingFloor->parent_path = $buildingArea->name . '/';
                    $buildingFloor->status = 1;
                    if (!$buildingFloor->save()) {
                        print_r($buildingFloor->errors);
                    }
                }
            }
        }
    }

    public function actionApartment($file_name = 'Import_Apartment.xlsx', $building_cluster_id = 3)
    {
        $fxls = dirname((dirname(__DIR__))) . "/db_desgin/" . $file_name;
        $spreadsheet = IOFactory::load($fxls);
        $xls_datas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $i = 0;
        $countCreate = 0;
        $arrayBuildingAreaEmpty = [];
        foreach ($xls_datas as $xls_data) {
            $i++;
            if ($i == 1) {
                continue;
            }
            $rows = [];
            $j = 0;
            $is_break = 0;
            $parent_path = '';
            foreach ($xls_data as $col) {
                if ($j == 0 && empty($col)) {
                    $is_break = 1;
                }
                $col = trim($col);
                if (!empty($col) && $j == 2) {
                    $parent_path = $col . '/';
                    list($col1, $col2) = explode('/', $col);
                    $col = $col1 . '/';
                }
                $rows[] = $col;
                $j++;
            }
            if ($is_break == 1) {
                break;
            }

            $buildingArea = BuildingArea::findOne(['building_cluster_id' => $building_cluster_id, 'name' => $col2, 'parent_path' => $rows[2]]);
            if (!empty($buildingArea)) {
                $apartment = new Apartment();
                $apartment->name = $rows[1];
                $apartment->generateCode($building_cluster_id);
                $apartment->parent_path = $parent_path;
                $apartment->capacity = (int)$rows[3];
                $apartment->building_cluster_id = $building_cluster_id;
                $apartment->building_area_id = $buildingArea->id;
                $apartment->save();
                $countCreate++;
            } else {
                $arrayBuildingAreaEmpty[] = ['line' => $i, 'parent_path' => $parent_path];
            }
        }
        echo $i . '===' . $countCreate;
        print_r($arrayBuildingAreaEmpty);
    }

    public function actionResident($file_name = 'Import_Resident.xlsx', $building_cluster_id = 3)
    {
        $fxls = dirname((dirname(__DIR__))) . "/db_desgin/" . $file_name;
        $spreadsheet = IOFactory::load($fxls);
        $xls_datas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $i = 0;
        $phoneErrors = [];
        $apartmentError = [];
        $residentUserExist = [];
        $residentUserCreateError = [];
        $apartmentMapResidentUserError = [];
        $countCreate = 0;
        foreach ($xls_datas as $xls_data) {
            $i++;
            if ($i == 1) {
                continue;
            }
            $rows = [];
            $j = 0;
            $is_break = 0;
            foreach ($xls_data as $col) {
                if ($j == 0 && empty($col)) {
                    $is_break = 1;
                }
                $col = trim($col);
                if ($j == 1) {
                    $col_new = CUtils::validateMsisdn($col);
                    if (empty($col_new)) {
                        $phoneErrors[] = ['line' => $i, 'phone' => $col];
                        continue;
                    }
                    $col = $col_new;
                }
                if ($j == 3) {
                    if ($col == 'Chủ hộ') {
                        $col = 1;
                    } else {
                        $col = 0;
                    }
                }
                if ($j == 5) {
                    $col = $col . '/';
                }
                $rows[] = $col;
                $j++;
            }
            if ($is_break == 1) {
                break;
            }
//            print_r($rows);
            $apartment = Apartment::findOne(['building_cluster_id' => $building_cluster_id, 'name' => $rows[4], 'parent_path' => $rows[5]]);
            if (empty($apartment)) {
                $apartmentError[] = $rows[5];
                continue;
            }
            $residentUser = ResidentUser::findByPhone($rows[1]);
            if (empty($residentUser)) {
                $residentUser = new ResidentUser();
                $residentUser->phone = $rows[1];
                $residentUser->setPassword(time());
                $residentUser->first_name = $rows[2];
                $residentUser->status = ResidentUser::STATUS_ACTIVE;
                if (!$residentUser->save()) {
                    $residentUserCreateError[] = ['line' => $i, 'messageError' => $residentUser->getErrors()];
                };
            } else {
                $residentUserExist[] = ['line' => $i, 'phone' => $rows[1]];
            }
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $apartment->id, 'type' => $rows[3], 'apartment_parent_path' => $apartment->parent_path, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            if (!empty($apartmentMapResidentUser)) {
                $apartmentMapResidentUserError[] = ['line' => $i, 'name' => $apartment->name, 'parent_path' => $apartment->parent_path];
                continue;
            }
            $apartmentMapResidentUser = new ApartmentMapResidentUser();
            $apartmentMapResidentUser->apartment_id = $apartment->id;
            $apartmentMapResidentUser->apartment_name = $apartment->name;
            $apartmentMapResidentUser->apartment_capacity = (int)$apartment->capacity;
            $apartmentMapResidentUser->apartment_code = $apartment->code;
            $apartmentMapResidentUser->apartment_parent_path = $apartment->parent_path;
            $apartmentMapResidentUser->resident_user_id = $residentUser->id;
            $apartmentMapResidentUser->resident_user_phone = $residentUser->phone;
            $apartmentMapResidentUser->resident_user_email = $residentUser->email;
            $apartmentMapResidentUser->resident_user_first_name = $residentUser->first_name;
            $apartmentMapResidentUser->resident_user_last_name = $residentUser->last_name;
            $apartmentMapResidentUser->resident_user_avatar = $residentUser->avatar;
            $apartmentMapResidentUser->resident_user_nationality = $residentUser->nationality;
            $apartmentMapResidentUser->resident_user_gender = $residentUser->gender;
            $apartmentMapResidentUser->resident_user_birthday = $residentUser->birthday;
            $apartmentMapResidentUser->resident_user_is_send_email = $residentUser->is_send_email;
            $apartmentMapResidentUser->resident_user_is_send_notify = $residentUser->is_send_notify;
            $apartmentMapResidentUser->building_cluster_id = $apartment->building_cluster_id;
            $apartmentMapResidentUser->building_area_id = $apartment->building_area_id;
            $apartmentMapResidentUser->type = $rows[3];
            $apartmentMapResidentUser->type_relationship = 7;
            if (!$apartmentMapResidentUser->save()) {
                $apartmentMapResidentUserCreateError[] = ['line' => $i, 'messageError' => $apartmentMapResidentUser->getErrors()];
            } else {
                $countCreate++;
            };
        }
        echo $i . '===' . $countCreate;
        print_r($phoneErrors);
        print_r($apartmentError);
        print_r($residentUserExist);
        print_r(count($residentUserExist));
        print_r($apartmentMapResidentUserError);
        print_r(count($apartmentMapResidentUserError));
    }

    public function actionServiceFee($file_name = 'import_demo.xlsx', $is_validate = 0, $service_map_management_id = 1)
    {
        $fxls = dirname((dirname(__DIR__))) . "/db_desgin/" . $file_name;
        $spreadsheet = IOFactory::load($fxls);
        $xls_datas = $spreadsheet->getSheet(0)->toArray(null, true, true, true);
        $building_cluster_id = 2;
        $service_map_management_id = 1;
        $serviceMapManagement = ServiceMapManagement::findOne(['id' => $service_map_management_id, 'building_cluster_id' => $building_cluster_id]);
        if (empty($serviceMapManagement)) {
            return false;
        }
        $i = 0;
        $imported = 0;
        $apartmentArrayError = [];
        $arrCreateError = [];
        foreach ($xls_datas as $xls_data) {
            $i++;
            if ($i == 1) {
                continue;
            }
            $rows = [];
            $j = 0;
            $is_break = 0;
            foreach ($xls_data as $col) {
                $col = trim($col);
                if ($j == 0 && empty($col)) {
                    $is_break = 1;
                }
                if ($j == 2) {
                    $col .= '/';
                } else if ($j == 5 && !empty($col)) {
                    list($month, $year) = explode('/', $col);
                    $col = $year . '-' . $month;
                } else if ($j == 6 && !empty($col)) {
                    list($day, $month, $year) = explode('/', $col);
                    $col = $year . '-' . $month . '-' . $day;
                }
                $rows[] = $col;
                $j++;
            }
            if ($is_break == 1) {
                break;
            }
            print_r($rows);
            continue;
            $apartment = ApartmentMapResidentUser::findOne(['building_cluster_id' => $building_cluster_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'apartment_name' => $rows[1], 'apartment_parent_path' => $rows[2]]);
            if (empty($apartment)) {
                $apartmentArrayError[] = [
                    'line' => $i + 1,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2],
                ];
            } else {
                if ($is_validate == 0) {
                    $servicePaymentFee = new ServicePaymentFee();
                    $servicePaymentFee->service_map_management_id = $service_map_management_id;
                    $servicePaymentFee->building_cluster_id = $building_cluster_id;
                    $servicePaymentFee->building_area_id = $apartment->building_area_id;
                    $servicePaymentFee->apartment_id = $apartment->id;
                    $servicePaymentFee->description = $rows[3];
                    $servicePaymentFee->status = ServicePaymentFee::STATUS_UNPAID;
                    $servicePaymentFee->price = $rows[4];
                    $servicePaymentFee->fee_of_month = strtotime($rows[5]);
                    $servicePaymentFee->day_expired = strtotime($rows[6]);
                    if (!$servicePaymentFee->save()) {
                        $arrCreateError[] = [
                            'line' => $i + 1,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2],
                            'errors' => $servicePaymentFee->errors
                        ];
                    };
                }
            }
            $imported++;
        }
        echo $imported . "\n";
        print_r($apartmentArrayError);
        print_r($arrCreateError);
    }
}