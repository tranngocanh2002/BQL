<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceManagementVehicle;
use common\models\ServiceMapManagement;
use common\models\ServiceParkingLevel;
use common\models\ServiceWaterFee;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;
use yii\helpers\Html;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceManagementVehicleImportForm")
 * )
 */
class ServiceManagementVehicleImportForm extends Model
{
    /**
     * @SWG\Property(description="is validate: 0 - là ko validate, 1 - validate", default="", type="integer")
     * @var integer
     */
    public $is_validate;

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
            [['is_validate', 'file_path'], 'required'],
            [['file_path'], 'string'],
        ];
    }

    public function import()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $is_validate = $this->is_validate;

        $fileandpath = \Yii::getAlias('@webroot') . $this->file_path;
        $spreadsheet = IOFactory::load($fileandpath);
//        $xls_datas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $xls_datas = $spreadsheet->getActiveSheet();
        $sheetNames = $spreadsheet->getSheetNames();

        $imported = 0;
        $apartmentMapResidentUserArrayError = [];
        $serviceParkingLevelError = [];
        $serviceManagementVehicleError = [];
        $arrCreateError = [];
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        while (true) {
            $rows = [];
            $stop = 0;
            $setSheet = 'Danh sách gửi xe';
            if ($sheetNames[0] !== $setSheet) {
                $apartmentMapResidentUserArrayError[] = [
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
                if($col == 'E'){
                    if(!empty($val)){
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
                }else if($col == 'G'){
                    if(!empty($val)){
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
                                'message' => Yii::t('frontend', "Ngày kết thúc không đúng định dạng")
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
            $rows[4] = trim($rows[4]);
            $rows[6] = trim($rows[6]);
            if("" == $rows[1])
            {
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2],
                    'message' => Yii::t('frontend', "Tên bất động sản không được để trống")
                ];
                continue;
            }
            if(empty($rows[2])){
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Biển số xe không được để trống")
                ];
                continue;
            }
            if(strlen($rows[2]) >= 20){
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2],
                    'message' => Yii::t('frontend', "Biển số xe không đúng định dạng")
                ];
                continue;
            }
            if(empty($rows[4])){
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Ngày gửi không được để trống")
                ];
                continue;
            }
            if(!is_numeric($rows[4])){
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2],
                    'message' => Yii::t('frontend', "Ngày gửi không đúng định dạng")
                ];
                continue;
            }

            if(empty($rows[5])){
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Mã phí không không được để trống")
                ];
                continue;
            }
            if(empty($rows[6])){
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Ngày kết thúc không được để trống")
                ];
                continue;
            }
            if(!is_numeric($rows[6])){
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Ngày kết thúc không đúng định dạng")
                ];
                continue;
            }
            if($rows[4] > $rows[6]){
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Ngày gửi hoặc ngày kết thúc không hợp lệ")
                ];
                continue;
            }
            
            // $rows[4] = trim($rows[4]);
            // $rows[6] = trim($rows[6]);


            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['building_cluster_id' => $building_cluster_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'apartment_name' => $rows[1]]);
            if (empty($apartmentMapResidentUser)) {
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Bất động sản không tồn tại hoặc chưa có chủ hộ" )
                ];
            } else {
                if ($is_validate == 0) {
                    $serviceParkingLevel = ServiceParkingLevel::find()->where(['building_cluster_id' => $building_cluster_id, 'code' => $rows[5]])->one();
                    if (empty($serviceParkingLevel)) {
                        $serviceParkingLevelError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'service_parking_level' => $rows[5],
                            'message' => Yii::t('frontend', "Mã phí không hợp lệ")
                        ];
                        continue;
                    }
                    $serviceManagementVehicle = ServiceManagementVehicle::findOne(['apartment_id' => $apartmentMapResidentUser->apartment_id, 'number' => $rows[2], 'status' => [ServiceManagementVehicle::STATUS_ACTIVE, ServiceManagementVehicle::STATUS_DEFAULT], 'is_deleted' => ServiceManagementVehicle::NOT_DELETED]);
                    if(!empty($serviceManagementVehicle)){
                        $serviceManagementVehicleError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'number' => $rows[4],
                            'message' => Yii::t('frontend', "Xe đã được khai báo sử dụng")
                        ];
                        continue;
                    }
                    $serviceManagementVehicle = new ServiceManagementVehicle();
                    $serviceManagementVehicle->building_cluster_id = $building_cluster_id;
                    $serviceManagementVehicle->building_area_id = $apartmentMapResidentUser->building_area_id;
                    $serviceManagementVehicle->apartment_id = $apartmentMapResidentUser->apartment_id;
                    $serviceManagementVehicle->number = $rows[2];
                    $serviceManagementVehicle->description = $rows[3];
                    $serviceManagementVehicle->start_date = $rows[4];
                    $serviceManagementVehicle->service_parking_level_id = $serviceParkingLevel->id;
                    $serviceManagementVehicle->status = ServiceManagementVehicle::STATUS_ACTIVE;
                    $serviceManagementVehicle->end_date = $rows[6];
                    $serviceManagementVehicle->tmp_end_date = $rows[6];
                    if (!$serviceManagementVehicle->save()) {
                        $arrCreateError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'errors' => $serviceManagementVehicle->errors,
                            'message' => Yii::t('frontend', "Khai báo xe không thành công")
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
            'serviceManagementVehicleError' => $serviceManagementVehicleError,
            'serviceParkingLevelError' => $serviceParkingLevelError,
            'arrCreateError' => $arrCreateError,
            'TotalRow' => $i - 2,
            'TotalImport' => $imported,
        ];
    }

    public function genForm()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $file_path = '/uploads/fee/' . time() . '-service-management-vehicle.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách gửi xe');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên bất động sản');
        $sheet->setCellValue('C1', 'Biển số xe');
        $sheet->setCellValue('D1', 'Mô tả');
        $sheet->setCellValue('E1', 'Ngày gửi');
        $sheet->setCellValue('F1', 'Mã phí');
        $sheet->setCellValue('G1', 'Ngày kết thúc');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFill()->getStartColor()->setARGB('10a54a');
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
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setWrapText(true);

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
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser) {
            if($i >= 3)
            {
                break;
            }
            if(isset($ids[$apartmentMapResidentUser->apartment_id])){ continue;}
            list($lo, $tang) = explode('/', $apartmentMapResidentUser->apartment_parent_path);
            $ids[$apartmentMapResidentUser->apartment_id] = $apartmentMapResidentUser->apartment_id;
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $apartmentMapResidentUser->apartment_name);
            $sheet->setCellValue('E'.$i, date('d/m/Y'));
            $sheet->getStyle('E'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');

            $validation = $sheet->getCell('F' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $arrLevels . '"');

            $sheet->setCellValue('G'.$i, date('d/m/Y'));
            $sheet->getStyle('G'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');

            $i++;
        }
        //Thêm sheet Hướng dẫn
        $wrapStyle = [
            'alignment' => [
                'wrapText' => true,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '10a54a',
                ],
            ],
        ];
         $spreadsheet->createSheet(); // Create a new sheet
         $sheet2 = $spreadsheet->getSheet(1); // Get the newly created sheet
         $sheet2->setTitle('Hướng dẫn'); // Set title for the new sheet
         $sheet2->setCellValue('A1', 'Dữ liệu');
         $sheet2->setCellValue('B1', 'Bắt buộc');
         $sheet2->setCellValue('C1', 'Quy tắc nhập');
         $arrColumns2 = ['A', 'B', 'C'];
         $sheet2->getStyle('A1:C1')->getFont()->setBold(true);
         $sheet2->getStyle('A1:C1')->getFill()->setFillType(Fill::FILL_SOLID);
         $sheet2->getStyle('A1:C1')->getFill()->getStartColor()->setARGB('10a54a');
         foreach ($arrColumns2 as $column){
             $w = 25;
             if($column == 'A'){
                 $w = 10;
             }
             $sheet2->getColumnDimension($column)->setWidth($w);
             $sheet2->getStyle($column.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
             $sheet2->getStyle($column.'1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
         }
         $sheet2->getRowDimension(1)->setRowHeight(25);
         $sheet2->getStyle('A1:C1')->getAlignment()->setWrapText(true);
         $sheet2->setCellValue('A2','STT');
         $sheet2->setCellValue('B2','Không');
         $sheet2->setCellValue('C2','- Chỉ được điền số');
 
        //  $sheet2->getStyle('A3:C3')->applyFromArray($wrapStyle);

         $sheet2->setCellValue('A3','Bất động sản');
         $sheet2->setCellValue('B3','Có');
         $cellValue = "- Giới hạn 10 ký tự bất kỳ\n- Không cho phép nhập có dấu, khoảng trống\n- Tên bất động sản tồn tại trên hệ thống\n- BĐS đã có chủ hộ";
         $sheet2->setCellValue('C3', Html::decode($cellValue));
         $sheet2->getStyle('C3')->getAlignment()->setWrapText(true);
 
         $sheet2->setCellValue('A4','Biển số xe');
         $sheet2->setCellValue('B4','Có');
         $cellValue = "- Gồm tối đa 20 ký tự bất kỳ\n- Biển số xe không được trùng nhau trên hệ thống ";
         $sheet2->setCellValue('C4', Html::decode($cellValue));
         $sheet2->getStyle('C4')->getAlignment()->setWrapText(true);
 
         $sheet2->setCellValue('A5','Mô tả');
         $sheet2->setCellValue('B5','Không');
         $sheet2->setCellValue('C5','- Cho phép nhập tối đa 1000 ký tự');

         $sheet2->setCellValue('A6','Ngày gửi');
         $sheet2->setCellValue('B6','Có');
         $cellValue = "- Định dạng: dd/mm/yyyy\n-Nhỏ hơn ngày kết thúc";
         $sheet2->setCellValue('C6', Html::decode($cellValue));
         $sheet2->getStyle('C6')->getAlignment()->setWrapText(true);

         $sheet2->setCellValue('A7','Mã phí');
         $sheet2->setCellValue('B7','Có');
         $sheet2->setCellValue('C7','- Cho phép chọn 1 mã phí tương ứng với loại xe do ban quản lý cấu hình trên hệ thống (VD: MX1 - Xe đạp)');
         
         $sheet2->setCellValue('A8','Ngày kết thúc');
         $sheet2->setCellValue('B8','Có');
         $sheet2->setCellValue('C8','- Định dạng: dd/mm/yyyy');

         // set sheet active là sheet 0
        $spreadsheet->setActiveSheetIndex(0);
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'success' => true,
            'file_path' => $file_path
        ];
    }
}
