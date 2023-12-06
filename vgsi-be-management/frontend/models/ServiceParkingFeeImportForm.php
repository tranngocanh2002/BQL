<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceManagementVehicle;
use common\models\ServiceMapManagement;
use common\models\ServiceParkingFee;
use common\models\ServiceParkingLevel;
use common\models\ServiceVehicleConfig;
use common\models\ServiceWaterFee;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
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
 *   @SWG\Xml(name="ServiceParkingFeeImportForm")
 * )
 */
class ServiceParkingFeeImportForm extends Model
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
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;

        //check nếu tắt chế độ tạo phí tự động thì mới cho import
        $serviceVehicleConfig = ServiceVehicleConfig::findOne(['building_cluster_id' => $building_cluster_id]);
        if(!empty($serviceVehicleConfig) && $serviceVehicleConfig->auto_create_fee === ServiceVehicleConfig::AUTO_CREATE_FEE){
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
        $serviceParkingLevelError = [];
        $serviceManagementVehicleError = [];
        $arrCreateError = [];
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        while (true) {
            $rows = [];
            $stop = 0;
            foreach ($arrColumns as $col) {
                $cell = $xls_datas->getCell($col . $i);
                $val = $cell->getFormattedValue();
                $val = trim($val);
                if ($col == 'A' && empty($val)) {
                    $stop = 1;
                    break;
                }
//                if ($col == 'C' && !empty($val)) {
//                    $val = $val .'/';
//                }
                if($col == 'F' || $col == 'G'){
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
            if ($stop == 1) {
                break;
            }
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
                    $serviceManagementVehicle = ServiceManagementVehicle::findOne(['apartment_id' => $apartmentMapResidentUser->apartment_id,'number' => $rows[4]]);
                    if(empty($serviceManagementVehicle)){
//                        continue;
                        $serviceManagementVehicleError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'number' => $rows[4],
                            'message' => Yii::t('frontend', "Xe chưa được khai báo")
                        ];
                        continue;
                        $serviceParkingLevel = ServiceParkingLevel::findOne(['code' => $rows[9]]);
                        if (empty($serviceParkingLevel)) {
                            $serviceParkingLevelError[] = [
                                'line' => $i - 2,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $rows[2].'/'.$rows[3],
                                'service_parking_level' => $rows[7],
                                'message' => Yii::t('frontend', "service parking level empty")
                            ];
                            continue;
                        }
                        $serviceManagementVehicle = new ServiceManagementVehicle();
                        $serviceManagementVehicle->building_cluster_id = $building_cluster_id;
                        $serviceManagementVehicle->building_area_id = $apartmentMapResidentUser->building_area_id;
                        $serviceManagementVehicle->apartment_id = $apartmentMapResidentUser->apartment_id;
                        $serviceManagementVehicle->number = $rows[4];
                        $serviceManagementVehicle->start_date = $rows[5];
                        $serviceManagementVehicle->service_parking_level_id = $serviceParkingLevel->id;
                        $serviceManagementVehicle->status = ServiceManagementVehicle::STATUS_ACTIVE;
                        $serviceManagementVehicle->end_date = $serviceManagementVehicle->start_date;
                        $serviceManagementVehicle->tmp_end_date = $serviceManagementVehicle->start_date;
                        if (!$serviceManagementVehicle->save()) {
                            $arrCreateError[] = [
                                'line' => $i - 2,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $rows[2].'/'.$rows[3],
                                'errors' => $serviceManagementVehicle->errors,
                                'message' => Yii::t('frontend', "Khai báo xe không thành công")
                            ];
                        };
                    }

                    if($rows[5] < $serviceManagementVehicle->tmp_end_date){
                        $serviceManagementVehicleError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'number' => $rows[4],
                            'message' => Yii::t('frontend', "Thời gian sử dụng không đúng")
                        ];
                        continue;
                    }

                    $serviceParkingFee = new ServiceParkingFee();
                    $serviceParkingFee->service_map_management_id = $service_map_management_id;
                    $serviceParkingFee->building_cluster_id = $building_cluster_id;
                    $serviceParkingFee->building_area_id = $apartmentMapResidentUser->building_area_id;
                    $serviceParkingFee->apartment_id = $apartmentMapResidentUser->apartment_id;
                    $serviceParkingFee->service_management_vehicle_id = $serviceManagementVehicle->id;
                    $serviceParkingFee->service_parking_level_id = $serviceManagementVehicle->service_parking_level_id;
                    $serviceParkingFee->fee_of_month = $rows[5];
                    $serviceParkingFee->start_time = $rows[5];
                    $serviceParkingFee->end_time = $rows[6];
                    $serviceParkingFee->total_money = $rows[7];
                    if($serviceParkingFee->total_money <= 0){
                        $arrCreateError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'message' => Yii::t('frontend', "Tổng tiền phí không phù hợp: ". $serviceParkingFee->total_money)
                        ];
                        continue;
                    }
                    $serviceParkingFee->description = $rows[8];
                    if (!$serviceParkingFee->save()) {
                        $arrCreateError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'errors' => $serviceParkingFee->errors,
                            'message' => Yii::t('frontend', "Tạo phí xe không thành công")
                        ];
                    }else{
                        $imported++;
                    };
                }
            }
        }
        $success = true;
        $message = Yii::t('frontend', "Import success");
        if($imported <= 0){
//            $success = false;
            $message = Yii::t('frontend', "Import Error");
        }
        return [
            'success' => $success,
            'message' => $message,
            'apartmentMapResidentUserArrayError' => $apartmentMapResidentUserArrayError,
            'serviceParkingLevelError' => $serviceParkingLevelError,
            'serviceManagementVehicleError' => $serviceManagementVehicleError,
            'arrCreateError' => $arrCreateError,
            'TotalRow' => $i - 2,
            'TotalImport' => $imported,
        ];
    }

    public function genForm()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $file_path = '/uploads/fee/'.time().'-service-parking-fee.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên căn hộ');
        $sheet->setCellValue('C1', 'Lô');
        $sheet->setCellValue('D1', 'Tầng');
        $sheet->setCellValue('E1', 'Biển số xe');
        $sheet->setCellValue('F1', 'Ngày bắt đầu');
        $sheet->setCellValue('G1', 'Ngày kết thúc');
        $sheet->setCellValue('H1', 'Tổng tiền');
        $sheet->setCellValue('I1', 'Mô tả');
        $sheet->setCellValue('J1', 'Mã phí');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $spreadsheet->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle('A1:J1')->getFill()->getStartColor()->setARGB('10a54a');
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
        $spreadsheet->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setWrapText(true);

        $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['building_cluster_id' => $building_cluster_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD])->all();
        $serviceParkingLevels = ServiceParkingLevel::find()->where(['building_cluster_id' => $building_cluster_id])->all();
        $arrLevels = '';
        foreach ($serviceParkingLevels as $serviceParkingLevel) {
            $arrLevels .= $serviceParkingLevel->code . ',';
        }
        $arrLevels = trim($arrLevels, ',');
        if(empty($arrLevels)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $i = 2;
        $ids = [];
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser){
            if(isset($ids[$apartmentMapResidentUser->apartment_id])){ continue;}
            list($lo, $tang) = explode('/', $apartmentMapResidentUser->apartment_parent_path);
            $ids[$apartmentMapResidentUser->apartment_id] = $apartmentMapResidentUser->apartment_id;
            $vehicles = ServiceManagementVehicle::find()->where(['apartment_id' => $apartmentMapResidentUser->apartment_id, 'is_deleted' => ServiceManagementVehicle::NOT_DELETED])->all();
            if(empty($vehicles)){
                $sheet->setCellValue('A'.$i, $i-1);
                $sheet->setCellValue('B'.$i, $apartmentMapResidentUser->apartment_name);
                $sheet->setCellValue('C'.$i, $lo);
                $sheet->setCellValue('D'.$i, $tang);
                $sheet->setCellValue('F'.$i, date('d/m/Y'));
                $sheet->getStyle('F'.$i)
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');
                $sheet->setCellValue('G'.$i, date('d/m/Y'));
                $sheet->getStyle('G'.$i)
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');

                $validation = $sheet->getCell('J' . $i)->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setFormula1('"' . $arrLevels . '"');

                $i++;
            }else{
                foreach ($vehicles as $vehicle){
                    $sheet->setCellValue('A'.$i, $i-1);
                    $sheet->setCellValue('B'.$i, $apartmentMapResidentUser->apartment_name);
                    $sheet->setCellValue('C'.$i, $lo);
                    $sheet->setCellValue('D'.$i, $tang);
                    $sheet->setCellValue('E'.$i, trim($vehicle->number));
                    $sheet->setCellValue('F'.$i, date('d/m/Y'));
                    $sheet->getStyle('F'.$i)
                        ->getNumberFormat()
                        ->setFormatCode('dd/mm/yyyy');
                    $sheet->setCellValue('G'.$i, date('d/m/Y'));
                    $sheet->getStyle('G'.$i)
                        ->getNumberFormat()
                        ->setFormatCode('dd/mm/yyyy');

                    $validation = $sheet->getCell('J' . $i)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1('"' . $arrLevels . '"');

                    $i++;
                }
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
