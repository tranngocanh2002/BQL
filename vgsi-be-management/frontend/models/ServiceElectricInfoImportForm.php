<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceBuildingConfig;
use common\models\ServiceElectricInfo;
use common\models\ServiceMapManagement;
use common\models\ServiceParkingLevel;
use common\models\ServiceElectricFee;
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
 *   @SWG\Xml(name="ServiceElectricInfoImportForm")
 * )
 */
class ServiceElectricInfoImportForm extends Model
{
    /**
     * @SWG\Property(description="is validate: 0 - là ko validate, 1 - validate", default="", type="integer")
     * @var integer
     */
    public $is_validate;

    /**
     * @SWG\Property(description="service_map_management_id", default="", type="integer")
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
            [['is_validate', 'file_path', 'service_map_management_id'], 'required'],
            [['file_path'], 'string'],
        ];
    }


    public function import()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $is_validate = $this->is_validate;
        $service_map_management_id = $this->service_map_management_id;

        $fileandpath = \Yii::getAlias('@webroot') . $this->file_path;
        $spreadsheet = IOFactory::load($fileandpath);
//        $xls_datas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $xls_datas = $spreadsheet->getActiveSheet();
        $sheetNames = $spreadsheet->getSheetNames();

        $imported = 0;
        $apartmentMapResidentUserArrayError = [];
        $ServiceElectricInfoError = [];
        $ServiceBuildingConfigError = [];
        $arrCreateError = [];
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D', 'E'];
        while (true) {
            $rows = [];
            $stop = 0;
            $setSheet = 'Danh sách sử dụng điện';
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
                if($col == 'C'){
                    // if(!empty($val) && !$this->isValidDate($val))
                    // if(empty($val))
                    // {
                    //     $val = 'c';
                    // }
                    // if(!empty($val) && $this->isValidDate($val)){
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
                        if(empty($val))
                        {
                            $ServiceElectricInfoError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày bắt đầu không đúng định dạng")
                            ];
                            break;
                        }
                    }else{
                        $val = null;
                    }
                }
                if($col == 'D'){
                    // if(!empty($val) && !$this->isValidDate($val))
                    // if(empty($val))
                    // {
                    //     $val = 'd';
                    // }
                    // if(!empty($val) && $this->isValidDate($val)){
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
                        if(empty($val))
                        {
                            $ServiceElectricInfoError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày kết thúc không đúng định dạng")
                            ];
                            break;
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
            
            if("" == $rows[1])
            {
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Tên bất động sản không được để trống")
                ];
                continue;
            }
            if(empty($rows[2])){
                $ServiceElectricInfoError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Ngày bắt đầu không được để trống")
                ];
                continue;
            }
            // if('c' == $rows[2]){
            //     $ServiceElectricInfoError[] = [
            //         'line' => $i - 2,
            //         'apartment_name' => $rows[1],
            //         'apartment_parent_path' => $rows[2].'/'.$rows[3],
            //         'message' => Yii::t('frontend', "Ngày bắt đầu không đúng định dạng")
            //     ];
            //     continue;
            // }
            if(empty($rows[3])){
                $ServiceElectricInfoError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Ngày kết thúc không được để trống")
                ];
                continue;
            }
            // if('d' == $rows[3]){
            //     $ServiceElectricInfoError[] = [
            //         'line' => $i - 2,
            //         'apartment_name' => $rows[1],
            //         'apartment_parent_path' => $rows[2].'/'.$rows[3],
            //         'message' => Yii::t('frontend', "Ngày kết thúc không đúng định dạng")
            //     ];
            //     continue;
            // }
            if(($rows[2] > $rows[3])){
                $ServiceElectricInfoError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Ngày bắt đầu không được lớn hơn ngày kết thúc")
                ];
                continue;
            }
            if("" == $rows[4]){
                $ServiceElectricInfoError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Số chốt cuối không được để trống")
                ];
                continue;
            }
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['building_cluster_id' => $building_cluster_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'apartment_name' => $rows[1]]);
            if (empty($apartmentMapResidentUser)) {
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Bất động sản không tồn tại hoặc chưa có chủ hộ")
                ];
            } else {
                if ($is_validate == 0) {
                    $ServiceElectricInfo = ServiceElectricInfo::findOne(['building_cluster_id' => $building_cluster_id, 'apartment_id' => $apartmentMapResidentUser->apartment_id, 'service_map_management_id' => $service_map_management_id]);
                    if(!empty($ServiceElectricInfo)){
                        $ServiceElectricInfoError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'message' => Yii::t('frontend', "Bất động sản đã được khai báo sử dụng")
                        ];
                        continue;
                    }
                    if(round($rows[4]) < 0 || !is_numeric($rows[4])){
                        $ServiceElectricInfoError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'message' => Yii::t('frontend', "Số chốt cuối không hợp lệ")
                        ];
                        continue;
                    }
        
                    $ServiceElectricInfo = new ServiceElectricInfo();
                    $ServiceElectricInfo->building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
                    $ServiceElectricInfo->building_area_id = $apartmentMapResidentUser->building_area_id;
                    $ServiceElectricInfo->apartment_id = $apartmentMapResidentUser->apartment_id;
                    $ServiceElectricInfo->service_map_management_id = $service_map_management_id;
                    $ServiceElectricInfo->start_date = $rows[2];
                    $ServiceElectricInfo->end_date = $rows[3];
                    $ServiceElectricInfo->tmp_end_date = $rows[3];
                    $ServiceElectricInfo->end_index = (int)$rows[4];
                    if (!$ServiceElectricInfo->save()) {
                        Yii::error($ServiceElectricInfo->errors);
                        $arrCreateError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'errors' => $ServiceElectricInfo->errors,
                            'message' => Yii::t('frontend', "Khai báo chỉ số sử dụng không thành công")
                        ];
                    }else{
                        $imported++;
                    }
                }
            }
        }
        return [
            'success' => true,
            'message' => Yii::t('frontend', "Import success"),
            'apartmentMapResidentUserArrayError' => $apartmentMapResidentUserArrayError,
            'ServiceElectricInfoError' => $ServiceElectricInfoError,
            'ServiceBuildingConfigError' => $ServiceBuildingConfigError,
            'arrCreateError' => $arrCreateError,
            'TotalRow' => $i - 2,
            'TotalImport' => $imported,
        ];
    }

    public function genForm()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $file_path = '/uploads/fee/' . time() . '-service-electric-info.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách sử dụng điện');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên bất động sản');
        $sheet->setCellValue('C1', 'Ngày bắt đầu');
        $sheet->setCellValue('D1', 'Ngày kết thúc');
        $sheet->setCellValue('E1', 'Số chốt cuối');

        $arrColumns = ['A', 'B', 'C', 'D', 'E'];
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFill()->getStartColor()->setARGB('10a54a');
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
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setWrapText(true);

        $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['building_cluster_id' => $building_cluster_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all();
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
            $sheet->setCellValue('C'.$i, date('d/m/Y'));
            $sheet->getStyle('C'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
            $sheet->setCellValue('D'.$i, date('d/m/Y'));
            $sheet->getStyle('D'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
            $sheet->setCellValue('E'.$i, 0);
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
 
         $sheet2->setCellValue('A4','Ngày bắt đầu');
         $sheet2->setCellValue('B4','Có');
         $cellValue = "- Định dạng: dd/mm/yyyy\n-Nhỏ hơn ngày kết thúc";
         $sheet2->setCellValue('C4', Html::decode($cellValue));
         $sheet2->getStyle('C4')->getAlignment()->setWrapText(true);
 
         $sheet2->setCellValue('A5','Ngày kết thúc');
         $sheet2->setCellValue('B5','Có');
         $sheet2->setCellValue('C5','- Định dạng: dd/mm/yyyy');

         $sheet2->setCellValue('A6','Số chốt cuối');
         $sheet2->setCellValue('B6','Có');
         $sheet2->setCellValue('C6','- Chỉ cho phép nhập số');

         // set sheet active là sheet 0
        $spreadsheet->setActiveSheetIndex(0);
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'success' => true,
            'file_path' => $file_path
        ];
    }
    public function isValidDate($date) {
        return (strtotime($date) !== false);
    }

}
