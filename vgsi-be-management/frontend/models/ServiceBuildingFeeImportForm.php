<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceBuildingConfig;
use common\models\ServiceBuildingInfo;
use common\models\ServiceManagementVehicle;
use common\models\ServiceMapManagement;
use common\models\ServiceBuildingFee;
use common\models\ServiceVehicleConfig;
use common\models\ServiceWaterFee;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceBuildingFeeImportForm")
 * )
 */
class ServiceBuildingFeeImportForm extends Model
{
    /**
     * @SWG\Property(description="is validate: 0 - là ko validate, 1 - validate", default="", type="integer")
     * @var integer
     */
    public $is_validate;

    /**
     * @SWG\Property(description="service map management id", default="", type="integer")
     * @var integer
     */
    public $service_map_management_id;

    /**
     * @SWG\Property(description="file path", default="", type="string")
     * @var string
     */
    public $file_path;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_map_management_id', 'is_validate', 'file_path'], 'required'],
            [['file_path'], 'string'],
            [['service_map_management_id', 'is_validate'], 'integer'],
        ];
    }

    public function import()
    {
        $time_current = time();
        $minute_current = (int)date('i');
        $hour_current = (int)date('H');
        $day_current = (int)date('d');
        $month_current = (int)date('m');
        $start_month = strtotime(date('Y-m-01 00:00:00', $time_current));
        $end_month = strtotime(date('Y-m-01 00:00:00', strtotime('+1 month', strtotime(date('Y-m-01', $time_current)))));

        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;

        //check nếu tắt chế độ tạo phí tự động thì mới cho import
        $serviceBuildingConfig = ServiceBuildingConfig::findOne(['building_cluster_id' => $building_cluster_id]);
        if(!empty($serviceBuildingConfig) && $serviceBuildingConfig->auto_create_fee === ServiceBuildingConfig::AUTO_CREATE_FEE){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Auto Create Fee Not Import"),
            ];
        }

        $service_map_management_id = $this->service_map_management_id;
        $is_validate = $this->is_validate;

        $fileandpath = \Yii::getAlias('@webroot') . $this->file_path;
        $spreadsheet = IOFactory::load($fileandpath);
//        $xls_datas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $xls_datas = $spreadsheet->getActiveSheet();
        $sheetNames = $spreadsheet->getSheetNames();

        $serviceMapManagement = ServiceMapManagement::findOne(['id' => $service_map_management_id, 'building_cluster_id' => $building_cluster_id]);
        if (empty($serviceMapManagement)) {
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
            ];
        }
        if($serviceMapManagement->status == ServiceMapManagement::STATUS_INACTIVE){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Dịch vụ đang ngừng cung cấp")
            ];
        }
        $imported = 0;
        $apartmentMapResidentUserArrayError = [];
        $serviceBuildingConfigError = [];
        $ServiceBuildingInfoError = [];
        $arrCreateError = [];
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        while (true) {
            $rows = [];
            $stop = 0;
            $setSheet = 'Danh sách phí quản lý chờ duyệt';
            if ($sheetNames[0] !== $setSheet) {
                $ServiceBuildingInfoError[] = [
                    'line' => $i - 1,
                    'message' => Yii::t('frontend', "File tải lên không đúng mẫu quy định")
                ];
                $i++;
                $stop = count($arrColumns);
            }
            if ($stop == count($arrColumns)) {
                break;
            }
            foreach ($arrColumns as $col) {
                $cell = $xls_datas->getCell($col . $i);
                $val = $cell->getFormattedValue();
                $val = trim($val);
                if (in_array($col, $arrColumns) && empty($val)) {
                    $stop++;
                }
            }
            if ($stop == count($arrColumns)) {
                break;
            }
            foreach ($arrColumns as $col) {
                $cell = $xls_datas->getCell($col . $i);
                $val = $cell->getFormattedValue();
                $val = trim($val);
                if ($col == 'A' && empty($val)) {
                    $val = '';
                }
//                if ($col == 'C' && !empty($val)) {
//                    $val = $val .'/';
//                }
                if($col == 'E' || $col == 'F'){
                    if (!empty($val)) {
                        $format = $cell->getStyle()->getNumberFormat()->getFormatCode();
                        if($format == 'General'){
                            $format = 'd/m/Y';
                        }
                        if($format == 'm/d/yyyy'){
                            // $format = 'd/m/yyyy'; // định dạng ngày tháng năm
                            // $val = strtotime(str_replace('/', '-', $val));
                            $format = 'm/d/Y';
                            $dateObj = \DateTime::createFromFormat($format, $val);
                            if ($dateObj == true && array_sum(\DateTime::getLastErrors()) === 0) {
                                $val = \DateTime::createFromFormat('m/d/Y', $val);
                                $val = $val->getTimestamp();
                            } else {
                                $val = null;
                            }
                        }
                        else
                        {
                            $format = str_replace(['dd', 'mm', 'yyyy'], ['d', 'm', 'Y'], $format);
                            $val = CUtils::convertStringToTimeStamp($val, $format);
                        }
                        if(empty($val)){
                            $apartmentMapResidentUserArrayError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày gửi không đúng định dạng")
                            ];
                            continue;
                        }
                    }else{
                        $val = null;
                    }
                }
                $rows[] = $val;

            }
            // if ($stop == count($arrColumns)) {
            //     break;
            // }
            $i++;
            if(count($arrColumns) > count($rows)){
                continue;
            }
            $rows[2] = trim($rows[2]);
            $rows[3] = trim($rows[3]);

            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['building_cluster_id' => $building_cluster_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'apartment_name' => $rows[1], 'apartment_parent_path' => $rows[2].'/'.$rows[3].'/']);
            if (empty($apartmentMapResidentUser)) {
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Căn hộ không tồn tại hoặc chưa có chủ hộ: " .$rows[1])
                ];
            } else {
                if ($is_validate == 0) {
                    $serviceBuildingConfig = ServiceBuildingConfig::findOne(['building_cluster_id' => $building_cluster_id]);
                    if(empty($serviceBuildingConfig)){
                        $serviceBuildingConfigError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'number' => $rows[4],
                            'message' => Yii::t('frontend', "Dịch vụ chưa được cấu hình")
                        ];
                        continue;
                    }

                    $ServiceBuildingInfo = ServiceBuildingInfo::findOne(['building_cluster_id' => $building_cluster_id, 'apartment_id' => $apartmentMapResidentUser->apartment_id, 'service_map_management_id' => $service_map_management_id]);
                    if(empty($ServiceBuildingInfo)){
                        $ServiceBuildingInfoError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'message' => Yii::t('frontend', "Chưa khai báo sử dụng dịch vụ")
                        ];
                        continue;
                    }

                    if($rows[3] < $ServiceBuildingInfo->tmp_end_date){
                        $ServiceBuildingInfoError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'message' => Yii::t('frontend', "Thời gian bắt đầu sử dụng không đúng")
                        ];
                        continue;
                    }

                    $ServiceBuildingFee = new ServiceBuildingFee();
                    $ServiceBuildingFee->service_map_management_id = $service_map_management_id;
                    $ServiceBuildingFee->building_cluster_id = $building_cluster_id;
                    $ServiceBuildingFee->building_area_id = $apartmentMapResidentUser->building_area_id;
                    $ServiceBuildingFee->apartment_id = $apartmentMapResidentUser->apartment_id;
                    $ServiceBuildingFee->service_building_config_id = $serviceBuildingConfig->id;
                    $ServiceBuildingFee->fee_of_month = $rows[4];
                    $ServiceBuildingFee->start_time = $rows[4];
                    $ServiceBuildingFee->end_time = $rows[5];
                    $ServiceBuildingFee->total_money = $rows[6];
                    if($ServiceBuildingFee->total_money <= 0){
                        $arrCreateError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'message' => Yii::t('frontend', "Tổng tiền phí không phù hợp: ". $ServiceBuildingFee->total_money)
                        ];
                        continue;
                    }
                    $ServiceBuildingFee->description = $rows[7];
                    if (!$ServiceBuildingFee->save()) {
                        $arrCreateError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'errors' => $ServiceBuildingFee->errors,
                            'message' => Yii::t('frontend', "Tạo phí dịch vụ không thành công")
                        ];
                    }else{
                        $imported++;
                    };
                }
            }
        }
        return [
            'success' => true,
            'message' => Yii::t('frontend', "Import success"),
            'apartmentMapResidentUserArrayError' => $apartmentMapResidentUserArrayError,
            'serviceBuildingConfigError' => $serviceBuildingConfigError,
            'ServiceBuildingInfoError' => $ServiceBuildingInfoError,
            'arrCreateError' => $arrCreateError,
            'TotalRow' => $i - 2,
            'TotalImport' => $imported,
        ];
    }

    public function genForm()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $file_path = '/uploads/fee/'.time().'-service-building-fee.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách phí quản lý chờ duyệt');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên căn hộ');
        $sheet->setCellValue('C1', 'Lô');
        $sheet->setCellValue('D1', 'Tầng');
        $sheet->setCellValue('E1', 'Ngày bắt đầu');
        $sheet->setCellValue('F1', 'Ngày kết thúc');
        $sheet->setCellValue('G1', 'Tổng tiền');
        $sheet->setCellValue('H1', 'Mô tả');
        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFill()->getStartColor()->setARGB('10a54a');
        foreach ($arrColumns as $column){
            $w = 25;
            if($column == 'A'){
                $w = 10;
            }
            $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth($w);
            $spreadsheet->getActiveSheet()->getStyle($column.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle($column.'1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }
        $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        $spreadsheet->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setWrapText(true);

        $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['building_cluster_id' => $building_cluster_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD])->all();
        $i = 2;
        $ids = [];
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser){
            if(isset($ids[$apartmentMapResidentUser->apartment_id])){ continue;}
            list($lo, $tang) = explode('/', $apartmentMapResidentUser->apartment_parent_path);
            $ids[$apartmentMapResidentUser->apartment_id] = $apartmentMapResidentUser->apartment_id;
            $vehicles = ServiceManagementVehicle::find()->where(['apartment_id' => $apartmentMapResidentUser->apartment_id, 'is_deleted' => ServiceManagementVehicle::NOT_DELETED])->all();
            foreach ($vehicles as $vehicle){
                $sheet->setCellValue('A'.$i, $i-1);
                $sheet->setCellValue('B'.$i, $apartmentMapResidentUser->apartment_name);
                $sheet->setCellValue('C'.$i, $lo);
                $sheet->setCellValue('D'.$i, $tang);
                $sheet->setCellValue('E'.$i, date('d/m/Y'));
                $sheet->getStyle('E'.$i)
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');
                $sheet->setCellValue('F'.$i, date('d/m/Y'));
                $sheet->getStyle('F'.$i)
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');
                $i++;
            }
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'success' => true,
            'file_path' => $file_path
        ];
    }
}
