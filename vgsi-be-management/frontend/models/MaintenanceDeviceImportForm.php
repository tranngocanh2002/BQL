<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\MaintenanceDevice;
use common\models\Job;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use setasign\Fpdi\PdfParser\Filter\LzwException;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;
use yii\helpers\Html;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentImportForm")
 * )
 */
class MaintenanceDeviceImportForm extends Model
{
    /**
     * @SWG\Property(description="is validate: 0 - là ko validate, 1 - validate", default=0, type="integer")
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
    public function genForm()
    {
        $file_path = '/uploads/fee/' . time() . '-maintenancedevice.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách thiết bị');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Mã thiết bị');
        $sheet->setCellValue('C1', 'Tên thiết bị');
        $sheet->setCellValue('D1', 'Vị trí');
        $sheet->setCellValue('E1', 'Loại thiết bị');
        $sheet->setCellValue('F1', 'Ngày bắt đầu bảo hành');
        $sheet->setCellValue('G1', 'Ngày kết thúc bảo hành');
        $sheet->setCellValue('H1', 'Ngày bắt đầu bảo trì');
        $sheet->setCellValue('I1', 'Bảo trì lặp lại');
        $sheet->setCellValue('J1', 'Mô tả');
        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G','H','I','J'];
        $spreadsheet->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle('A1:J1')->getFill()->getStartColor()->setARGB('10a54a');
        foreach ($arrColumns as $column) {
            $w = 25;
            if ($column == 'A') {
                $w = 10;
            }
            $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth($w);
            $spreadsheet->getActiveSheet()->getStyle($column . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle($column . '1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }
        $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        $spreadsheet->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setWrapText(true);
        $type_list = MaintenanceDevice::$type_list;
        $type_list = implode(',', $type_list);
        $cycle_list = MaintenanceDevice::$cycle_list;
        $cycle_list = implode(',', $cycle_list);        
        for ($i=2; $i < 5; $i++){
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, 'M00'.($i-1));
            $sheet->setCellValue('C' . $i, 'Camera IP'.($i-1));
            $sheet->setCellValue('D' . $i, 'Tầng 1');
            $rand = rand(0, 4);
            $sheet->setCellValue('E' . $i, MaintenanceDevice::$type_list[$rand]);
            $validation = $sheet->getCell('E' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $type_list . '"');
            $sheet->setCellValue('F' . $i, '01/01/2023');
            $sheet->setCellValue('G' . $i, '01/01/2023');
            $sheet->setCellValue('H' . $i, '01/01/2023');
            $sheet->setCellValue('I' . $i, MaintenanceDevice::$cycle_list[0]);
            $validation = $sheet->getCell('I' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $cycle_list . '"');
            $sheet->setCellValue('J' . $i, 'Mô tả thiết bị');
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

        $sheet2->setCellValue('A3','Mã thiết bị');
        $sheet2->setCellValue('B3','Có');
        $cellValue = "- Tối đa 255 ký tự chữ và số";
        $sheet2->setCellValue('C3', Html::decode($cellValue));
        $sheet2->getStyle('C3')->getAlignment()->setWrapText(true);

        $sheet2->setCellValue('A4','Tên thiết bị');
        $sheet2->setCellValue('B4','Có');
        $cellValue = "- Tối đa 255 ký tự chữ và số";
        $sheet2->setCellValue('C4', Html::decode($cellValue));
        $sheet2->getStyle('C4')->getAlignment()->setWrapText(true);

        $sheet2->setCellValue('A5','Vị trí');
        $sheet2->setCellValue('B5','Không');
        $sheet2->setCellValue('C5','- Tối đa 255 ký tự chữ và số');

        $sheet2->setCellValue('A6','Loại thiết bị');
        $sheet2->setCellValue('B6','Có');
        $cellValue = "- Phải chọn 1 trong các giá trị: Máy tính, Quạt, Camera, Đèn, Thang máy\n- Không cho nhập các giá trị khác";
        $sheet2->setCellValue('C6', Html::decode($cellValue));
        $sheet2->getStyle('C6')->getAlignment()->setWrapText(true);

        $sheet2->setCellValue('A7','Ngày bắt đầu bảo hành');
        $sheet2->setCellValue('B7','Không');
        $cellValue = "- Định dạng: dd/mm/yyyy \n- Ngày bắt đầu bảo hành bắt buộc phải nhỏ hơn ngày kết thúc bảo hành";
        $sheet2->setCellValue('C7', Html::decode($cellValue));
        $sheet2->getStyle('C7')->getAlignment()->setWrapText(true);
        
        $sheet2->setCellValue('A8','Ngày kết thúc bảo hành');
        $sheet2->setCellValue('B8','Không');
        $cellValue = "- Định dạng: dd/mm/yyyy \n- Ngày bắt đầu bảo hành bắt buộc phải nhỏ hơn ngày kết thúc bảo hành";
        $sheet2->setCellValue('C8', Html::decode($cellValue));
        $sheet2->getStyle('C8')->getAlignment()->setWrapText(true);

        $sheet2->setCellValue('A9','Ngày bắt đầu bảo trì');
        $sheet2->setCellValue('B9','Có');
        $cellValue = "- Định dạng: dd/mm/yyyy \n- Chỉ được chọn từ ngày hiện tại \n- Ghi chú: từ dữ liệu này và chu kỳ lặp lại, hệ thống sẽ tính toán [Ngày bảo trì sắp tới]";
        $sheet2->setCellValue('C9', Html::decode($cellValue));
        $sheet2->getStyle('C9')->getAlignment()->setWrapText(true);

        $sheet2->setCellValue('A10','Bảo trì lặp lại');
        $sheet2->setCellValue('B10','Có');
        $cellValue = "- Phải chọn 1 trong các giá trị: 1 tháng, 2 tháng, 3 tháng, 6 tháng, 12 tháng, 24 tháng \n- Không cho nhập các giá trị khác";
        $sheet2->setCellValue('C10', Html::decode($cellValue));
        $sheet2->getStyle('C10')->getAlignment()->setWrapText(true);

        $sheet2->setCellValue('A11','Mô tả');
        $sheet2->setCellValue('B11','Không');
        $sheet2->setCellValue('C11','');
        // set sheet active là sheet 0
        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'success' => true,
            'file_path' => $file_path
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
        $apartmentMapResidentUserApprovedError = [];
        $maintenanceDeviceImportError = [];
        $arrCreateError = [];
        $arrIndexError = [];
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D', 'E','F','G','H','I','J'];
        while (true) {
            $rows = [];
            $stop = 0;
            $setSheet = 'Danh sách thiết bị';
            if ($sheetNames[0] !== $setSheet) {
                $maintenanceDeviceImportError[] = [
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
                if ($col == 'E') {
                    if(empty($val))
                    {
                        $maintenanceDeviceImportError[] = [
                            'line' => $i - 1,
                            'maintenance_device' => $rows[1],
                            'message' => Yii::t('frontend', "Loại thiết bị không được để trống")
                        ];
                        break;
                    }
                    $val = MaintenanceDevice::$type_list_number[$val] ?? -1;
                }
                if ($col == 'F') {
                    if (!empty($val)) {
                        $format = $cell->getStyle()->getNumberFormat()->getFormatCode();
                        if($format == 'General'){
                            $format = 'd/m/Y';
                        }
                        if($format == 'm/d/yyyy'){
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
                            $maintenanceDeviceImportError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày bắt đầu bảo hành không đúng định dạng")
                            ];
                            break;
                        }
                    }
                }
                if ($col == 'G') {
                    if (!empty($val)) {
                        $format = $cell->getStyle()->getNumberFormat()->getFormatCode();
                        if($format == 'General'){
                            $format = 'd/m/Y';
                        }
                        if($format == 'm/d/yyyy'){
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
                            $maintenanceDeviceImportError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày kết thúc bảo hành không đúng định dạng")
                            ];
                            break;
                        }
                    }
                }
                if ($col == 'H') {
                    if (!empty($val)) {
                        $format = $cell->getStyle()->getNumberFormat()->getFormatCode();
                        if($format == 'General'){
                            $format = 'd/m/Y';
                        }
                        if($format == 'm/d/yyyy'){
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
                            $maintenanceDeviceImportError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày bắt đầu bảo trì không đúng định dạng")
                            ];
                            break;
                        }
                    }else{
                        $maintenanceDeviceImportError[] = [
                            'line' => $i - 1,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => '',
                            'message' => Yii::t('frontend', "Ngày bắt đầu bảo trì không được để trống")
                        ];
                        break;
                    }
                    if($val < time()){
                        $maintenanceDeviceImportError[] = [
                            'line' => $i - 1,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => '',
                            'message' => Yii::t('frontend', "Ngày bắt đầu bảo trì không cho phép nhập ngày trong quá khứ")
                        ];
                        break;
                    }
                }
                if ($col == 'I') {
                    if (empty($val)) {
                        $maintenanceDeviceImportError[] = [
                            'line' => $i - 1,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => '',
                            'message' => Yii::t('frontend', "Bảo trì lặp lại không được để trống")
                        ];
                        break;
                    }
                    $val = MaintenanceDevice::$cycle_list_number[$val] ?? -1;
                }

                $rows[] = $val;
            }
            $i++;
            if (count($arrColumns) > count($rows)) {
                continue;
            }
            if (!empty($rows[0]) && !is_numeric($rows[0])) {
                $maintenanceDeviceImportError[] = [
                    'line' => $i - 2,
                    'maintenance_device' => $rows[0],
                    'message' => Yii::t('frontend', "Số thứ tự chỉ được nhập số")
                ];
                continue;
            }
            if (strlen($rows[1]) >= 255) {
                $maintenanceDeviceImportError[] = [
                    'line' => $i - 2,
                    'maintenance_device' => $rows[1],
                    'message' => Yii::t('frontend', "Mã thiết bị chi cho phép nhập tối đã 255 ký tự")
                ];
                continue;
            }
            if("" == $rows[1])
            {
                $maintenanceDeviceImportError[] = [
                    'line' => $i - 2,
                    'maintenance_device' => $rows[1],
                    'message' => Yii::t('frontend', "Mã thiết bị không được để trống")
                ];
                continue;
            }
            if (preg_match('/[@#$%^&*()\-+!.,\';:"`~=<>?|]/', $rows[1])) {
                $maintenanceDeviceImportError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => "",
                    'message' => Yii::t('frontend', "Mã thiết bị chỉ được nhập chữ và số")
                ];
                continue;
            }
            if(empty($rows[2]))
            {
                $maintenanceDeviceImportError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => "",
                    'message' => Yii::t('frontend', "Tên thiết bị không được để trống")
                ];
                continue;
            }
            if (preg_match('/[@#$%^&*()\-+!.,\';:"`~=<>?|]/', $rows[2])) {
                $maintenanceDeviceImportError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => "",
                    'message' => Yii::t('frontend', "Tên thiết bị chỉ được nhập chữ và số")
                ];
                continue;
            }
            if (strlen($rows[2]) >= 255) {
                $maintenanceDeviceImportError[] = [
                    'line' => $i - 2,
                    'maintenance_device' => $rows[1],
                    'message' => Yii::t('frontend', "Tên thiết bị chi cho phép nhập tối đã 255 ký tự")
                ];
                continue;
            }
            if (preg_match('/[@#$%^&*()\-+!.,\';:"`~=<>?|]/', $rows[3])) {
                $maintenanceDeviceImportError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => "",
                    'message' => Yii::t('frontend', "Vị trí chỉ được nhập chữ và số")
                ];
                continue;
            }
            if (strlen($rows[3]) >= 255) {
                $maintenanceDeviceImportError[] = [
                    'line' => $i - 2,
                    'maintenance_device' => $rows[1],
                    'message' => Yii::t('frontend', "Vị trí chi cho phép nhập tối đã 255 ký tự")
                ];
                continue;
            }
            if($rows[4] === -1){
                $maintenanceDeviceImportError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => "",
                    'message' => Yii::t('frontend', "Loại thiết bị không tồn tại trên hệ thống")
                ];
                continue;
            }
            if($rows[5] > $rows[6]){
                $maintenanceDeviceImportError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[5].'/'.$rows[6],
                    'message' => Yii::t('frontend', "Ngày bắt đầu bảo hành phải nhỏ hơn ngày kết thúc bảo hành")
                ];
                continue;
            }
            if($rows[8] === -1){
                $maintenanceDeviceImportError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => "",
                    'message' => Yii::t('frontend', "Bảo trì lặp lại không tồn tại trong hệ thống")
                ];
                continue;
            }
            if ($is_validate == 0) {
                $maintenanceDevice = new MaintenanceDevice();
                $maintenanceDevice->code = $rows[1];
                $maintenanceDevice->name = $rows[2];
                $maintenanceDevice->position = $rows[3];
                $maintenanceDevice->type = $rows[4];
                $maintenanceDevice->guarantee_time_start = $rows[5];
                $maintenanceDevice->guarantee_time_end = $rows[6];
                $maintenanceDevice->maintenance_time_start = $rows[7];
                $maintenanceDevice->cycle = $rows[8];
                $maintenanceDevice->description = $rows[9];
                $maintenanceDevice->building_cluster_id = $building_cluster_id;
                if (!$maintenanceDevice->save()) {
                    Yii::error($maintenanceDevice->errors);
                    $arrCreateError[] = [
                        'line' => $i - 2,
                        'apartment_name' => $rows[1],
                        'apartment_parent_path' => $rows[2].'/'.$rows[3],
                        'errors' => $maintenanceDevice->errors,
                        'message' => Yii::t('frontend', "Thêm mới thiết bị không thành công")
                    ];
                } else{
                    $imported++;
                }
            }
            
        }
        Yii::error([
            'apartmentMapResidentUserArrayError' => $apartmentMapResidentUserArrayError,
            'apartmentMapResidentUserApprovedError' => $apartmentMapResidentUserApprovedError,
            'maintenanceDeviceImportError' => $maintenanceDeviceImportError,
            'arrCreateError' => $arrCreateError,
            'arrIndexError' => $arrIndexError,
        ]);
        $success = true;
        $message = Yii::t('frontend', "Import success");
        if ($imported <= 0) {
//            $success = false;
            $message = Yii::t('frontend', "Import Error");
        }
        return [
            'success' => $success,
            'message' => $message,
            'apartmentMapResidentUserArrayError' => $apartmentMapResidentUserArrayError,
            'apartmentMapResidentUserApprovedError' => $apartmentMapResidentUserApprovedError,
            'arrCreateError' => $arrCreateError,
            'arrIndexError' => $arrIndexError,
            'maintenanceDeviceImportError' => $maintenanceDeviceImportError,
            'TotalRow' => $i - 2,
            'TotalImport' => $imported,
        ];
    }

}
