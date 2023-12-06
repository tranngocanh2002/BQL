<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingArea;
use common\models\ServiceBuildingConfig;
use common\models\ServiceMapManagement;
use common\models\ServiceParkingLevel;
use common\models\ServiceWaterFee;
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

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentImportForm")
 * )
 */
class ApartmentImportForm extends Model
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


    public function import()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $is_validate = $this->is_validate;

        $fileandpath = \Yii::getAlias('@webroot') . $this->file_path;
        $spreadsheet = IOFactory::load($fileandpath);
        $xls_datas = $spreadsheet->getActiveSheet();
        $sheetNames = $spreadsheet->getSheetNames();

        $imported = 0;
        $apartmentArrayError = [];
        $ApartmentError = [];
        $arrayBuildingAreaEmpty = [];
        $arrCreateError = [];
        $type_name_list = Yii::$app->params['Apartment_form_type_name_list'];
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $check_dt = false;
        while (true) {
            $rows = [];
            $stop = 0;
            $setSheet = 'Danh sách bất động sản';
            if ($sheetNames[0] !== $setSheet) {
                $apartmentArrayError[] = [
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
                if ($col == 'C') {
                    if (empty($val)) {
                        $val = 0;
                    }else{
                        $check_dt = true;
                        if(!preg_match('/^[0-9.]+$/', $val)) {
                            $apartmentArrayError[] = [
                                'line' => $i - 1,
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', 'Diện tích chỉ cho phép nhập ký tự số và dấu "."')
                            ];
                            continue;
                        }
                    }
                    $val = round($val, 2);
                    $val = (float)$val;
                }else if ($col == 'F') {
                    if (empty($val)) {
                        $val = 0;
                    }else{
                        if (!preg_match('/^[0-9]+$/', $val)) {
                            $apartmentArrayError[] = [
                                'line' => $i - 1,
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Số thành viên chỉ cho phép nhập ký tự số")
                            ];
                            continue;
                        }
                    }
                    $val = (int)$val;
                }

                if ($col == 'D') {
                    if(empty($val)){
                        $apartmentArrayError[] = [
                            'line' => $i - 1,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => '',
                            'message' => Yii::t('frontend', "Tình trạng bàn giao không được để trống")
                        ];
                        continue;
                    }
                    $val = Apartment::$handed_list_text[$val] ?? -1;
                }

                if ($col == 'E') {
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
                            $apartmentArrayError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày bàn giao không đúng định dạng")
                            ];
                            continue;
                        }
                    }else{
                        $val = null;
                    }
                }
                if ($col == 'G') {
                    $val = $type_name_list[$val] ?? null;
                }
                $rows[] = $val;

            }
            // if ($stop == count($arrColumns)) {
            //     break;
            // }
            $i++;
            if (count($arrColumns) > count($rows)) {
                continue;
            }
            Yii::info($rows);
            $apartment_parent_path = $rows[7] . '/' . $rows[8] . '/' . $rows[9];

            if (!preg_match('/^[a-zA-Z0-9.\-_]+$/', $rows[1])) {
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Tên bất động sản không được chứa dấu và khoảng trống")
                ];
                continue;
            }
            if (strlen($rows[1]) > 10) {
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Tên bất động sản chỉ cho phép nhập tối đa 10 ký tự")
                ];
                continue;
            }
            if(!isset(Apartment::$handed_list[$rows[3]])){
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Tình trạng bàn giao không phù hợp")
                ];
                continue;
            }
            if($rows[3] == Apartment::STATUS_HANDED && empty($rows[4])){
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Ngày bàn giao không được để trống")
                ];
                continue;
            }

            if(!empty($rows[4]) && $rows[4] > time()){
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Ngày bàn giao không hợp lệ")
                ];
                continue;
            }

            if(empty($rows[5])){
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Số thành viên không được để trống")
                ];
                continue;
            }
            if (strlen($rows[5]) > 5) {
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Số thành viên cho phép tối đa 5 ký tự")
                ];
                continue;
            }
            if(empty($rows[6])){
                if ($rows[6] !== 0) {
                    $apartmentArrayError[] = [
                        'line' => $i - 2,
                        'apartment_name' => $rows[1],
                        'apartment_parent_path' => $apartment_parent_path,
                        'message' => Yii::t('frontend', "Loại bất động sản không được để trống")
                    ];
                    continue;
                }
            }
            
            if (empty($rows[7])) {
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Địa chỉ không được để trống")
                ];
                continue;
            }
            if (strlen($rows[7]) > 50 || strlen($rows[8]) > 50 || strlen($rows[9]) > 50) {
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Địa chỉ cho phép nhập tối đa 50 ký tự")
                ];
                continue;
            }
            if (preg_match('/[@#$%^&*()\-+!.,\';:"`~=<>?|]/', $rows[7]) || preg_match('/[@#$%^&*()\-+!.,\';:"`~=<>?|]/', $rows[8]) || preg_match('/[@#$%^&*()\-+!.,\';:"`~=<>?|]/', $rows[9])) {
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Địa chỉ cho phép nhập ký tự chữ và số")
                ];
                continue;
            }
            if($check_dt === false){
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Diện tích không được để trống" )
                ];
                continue;
            }
            if(strlen($rows[2]) > 8){
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Diện tích cho phép tối đa 8 ký tự" )
                ];
                continue;
            }
            $area = self::createBuildingArea($building_cluster_id, $rows[7], BuildingArea::TYPE_AREA);
            $building = self::createBuildingArea($building_cluster_id, $rows[8], BuildingArea::TYPE_BUILDING, $area->id, $area->name . '/');
            $floor = self::createBuildingArea($building_cluster_id, $rows[9], BuildingArea::TYPE_FLOOR, $building->id, $area->name . '/' . $building->name . '/');
            $apartment = Apartment::findOne(['building_cluster_id' => $building_cluster_id, 'name' => $rows[1], 'is_deleted' => Apartment::NOT_DELETED]);
            if (!empty($apartment)) {
                Yii::error('apartment exist ' . $rows[1]);
                //đã tồn tại
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Bất động sản đã tồn tại:") . " " .$rows[1]
                ];
            } else if($rows[2] <= 0) {
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Diện tích bất động sản không phù hợp" )
                ];
            } else {
                if ($is_validate == 0) {
                    if (!empty($floor)) {
                        $apartment = new Apartment();
                        $apartment->name = $rows[1];
                        $apartment->generateCode($building_cluster_id);
                        $apartment->parent_path = $floor->parent_path . $floor->name . '/';
                        $apartment->capacity = $rows[2];
                        $apartment->total_members = $rows[5];
                        if(empty($apartment->total_members)){
                            $apartment->total_members = 0;
                        }
                        $apartment->date_delivery = $rows[4];
                        $apartment->form_type = $rows[6];
                        $apartment->building_cluster_id = $building_cluster_id;
                        $apartment->building_area_id = $floor->id;
                        if (!$apartment->save()) {
                            Yii::error('apartment create error ' . $rows[1]);
                            Yii::error($apartment->errors);
                            $message = "Thêm bất động sản không thành công: " .$rows[1];
                            if($apartment->errors){
                                foreach ($apartment->errors as $k => $v){
                                    $message = $v[0];
                                }
                            }
                            $arrCreateError[] = [
                                'line' => $i - 2,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $apartment_parent_path,
                                'message' => $message
                            ];
                        }else{
                            $imported++;
                        }
                    } else {
                        Yii::error('BuildingAreaEmpty ' . $rows[1]);
                        $arrayBuildingAreaEmpty[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $apartment_parent_path,
                            'message' => Yii::t('frontend', "Tầng chứa bất động sản không phù hợp: ") .$rows[1]
                        ];
                    }
                }
            }
        }
        $success = true;
        $message = Yii::t('frontend', "Import success");
        if ($imported <= 0) {
//            $success = false;
            $message = Yii::t('frontend', "Import Error");
        }
        return [
            'success' => $success,
            'message' => $message,
            'apartmentArrayError' => $apartmentArrayError,
            'ApartmentError' => $ApartmentError,
            'ArrayBuildingAreaEmpty' => $arrayBuildingAreaEmpty,
            'arrCreateError' => $arrCreateError,
            'TotalRow' => $i - 2,
            'TotalImport' => $imported,
        ];
    }

    private function createBuildingArea($building_cluster_id, $building_name, $type = BuildingArea::TYPE_BUILDING, $parent_id = null, $parent_path = null){
        $building = BuildingArea::findOne(['building_cluster_id' => $building_cluster_id, 'name' => $building_name, 'is_deleted' => BuildingArea::NOT_DELETED, 'parent_id' => $parent_id, 'type' => $type]);
        if(empty($building)){
            $building = new BuildingArea();
            $building->building_cluster_id = $building_cluster_id;
            $building->status = 1;
            $building->name = $building_name;
            $building->description = $building_name;
            $building->short_name = $building_name;
            $building->type = $type;
            $building->parent_id = $parent_id;
            $building->parent_path = $parent_path;
            if(!$building->save()){
                Yii::error('Tạo toà nhà không thành công');
            }
        }
        return $building;
    }

    public function genFormOld()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $file_path = '/uploads/fee/' . time() . '-apartment.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách bất động sản');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên căn hộ');
        $sheet->setCellValue('C1', 'Lô');
        $sheet->setCellValue('D1', 'Tầng');
        $sheet->setCellValue('E1', 'Diện tích');
        $sheet->setCellValue('F1', 'Số thành viên');
        $sheet->setCellValue('G1', 'Loại căn hộ');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFill()->getStartColor()->setARGB('10a54a');
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
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setWrapText(true);

        $i = 2;
        $buildingAreas = BuildingArea::find()->where(['building_cluster_id' => $buildingCluster->id, 'is_deleted' => BuildingArea::NOT_DELETED, 'parent_id' => null])->orderBy(['name' => SORT_ASC])->all();
        if(empty($buildingAreas)){
            return [
                'success' => false,
                'message' => Yii::t('frontend', 'Cần khai báo thông tin mặt bằng trước')
            ];
        }
        $type_list = Yii::$app->params['Apartment_form_type_list'];
        $type_list_str = implode(',', $type_list);
        foreach ($buildingAreas as $buildingArea){
            $floors = BuildingArea::find()->where(['building_cluster_id' => $buildingCluster->id, 'is_deleted' => BuildingArea::NOT_DELETED, 'parent_id' => $buildingArea->id])->all();
            foreach ($floors as $floor){
                $apartments = Apartment::find()->where(['building_area_id' => $floor->id, 'is_deleted' => Apartment::NOT_DELETED])->all();
                if(!empty($apartments)){
                    foreach ($apartments as $apartment){
                        $sheet->setCellValue('A' . $i, $i - 1);
                        $sheet->setCellValue('B' . $i, $apartment->name);
                        $sheet->setCellValue('C' . $i, trim($floor->parent_path, '/'));
                        $sheet->setCellValue('D' . $i, trim($floor->name, '/'));
                        $sheet->setCellValue('E' . $i, $apartment->capacity);
                        $sheet->setCellValue('F' . $i, $apartment->total_members);
                        $sheet->setCellValue('G' . $i, isset($type_list[$apartment->form_type]) ? $type_list[$apartment->form_type] : '');
                        $validation = $sheet->getCell('G' . $i)->getDataValidation();
                        $validation->setType(DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setFormula1('"' . $type_list_str . '"');
                        $i++;
                    }
                }else{
                    $sheet->setCellValue('A' . $i, $i - 1);
                    $sheet->setCellValue('B' . $i, $buildingArea->short_name .'.'.$floor->short_name.'.00'.$i);
                    $sheet->setCellValue('C' . $i, trim($floor->parent_path, '/'));
                    $sheet->setCellValue('D' . $i, trim($floor->name, '/'));
                    $sheet->setCellValue('E' . $i, 200);
                    $sheet->setCellValue('F' . $i, 2);
                    $validation = $sheet->getCell('G' . $i)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1('"' . $type_list_str . '"');
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

    public function exportOld()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $file_path = '/uploads/fee/' . time() . '-apartment-list.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách bất động sản');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên căn hộ');
        $sheet->setCellValue('C1', 'Lô');
        $sheet->setCellValue('D1', 'Tầng');
        $sheet->setCellValue('E1', 'Diện tích');
        $sheet->setCellValue('F1', 'Số thành viên');
        $sheet->setCellValue('G1', 'Mã căn');
        $sheet->setCellValue('H1', 'Loại căn');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFill()->getStartColor()->setARGB('10a54a');
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
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getAlignment()->setWrapText(true);

        $i = 2;
        $apartments = Apartment::find()->where(['building_cluster_id' => $buildingCluster->id, 'is_deleted' => Apartment::NOT_DELETED])->orderBy(['name' => SORT_ASC])->all();
        foreach ($apartments as $apartment) {
            $type_list = $apartment->getFormTypeList();
            $lo = '';
            $tang = '';
            if($apartment->parent_path){
                list($lo, $tang) = explode('/', $apartment->parent_path);
            }
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $apartment->name);
            $sheet->setCellValue('C' . $i, $lo);
            $sheet->setCellValue('D' . $i, $tang);
            $sheet->setCellValue('E' . $i, $apartment->capacity);
            $sheet->setCellValue('F' . $i, $apartment->total_members);
            $sheet->setCellValue('G' . $i, $apartment->code);
            $sheet->setCellValue('H' . $i, isset($type_list[$apartment->form_type]) ? $type_list[$apartment->form_type] : '');
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path
        ];
    }

    public function genForm()
    {
        $file_path = '/uploads/fee/' . time() . '-apartment.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách bất động sản');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên bất động sản');
        $sheet->setCellValue('C1', 'Diện tích');
        $sheet->setCellValue('D1', 'Tình trạng bàn giao');
        $sheet->setCellValue('E1', 'Ngày bàn giao');
        $sheet->setCellValue('F1', 'Số thành viên');
        $sheet->setCellValue('G1', 'Loại bất động sản');
        $sheet->setCellValue('H1', 'Cấu trúc cấp 1');
        $sheet->setCellValue('I1', 'Cấu trúc cấp 2');
        $sheet->setCellValue('J1', 'Cấu trúc cấp 3');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
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

        $type_list = Yii::$app->params['Apartment_form_type_list'];
        $type_list_str = implode(',', $type_list);

        $handed_list = Apartment::$handed_list;
        $handed_list_str = implode(',', $handed_list);
        for ($i=2; $i < 9; $i++){
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, 'A01.0'.($i-1));
            $sheet->setCellValue('C' . $i, rand(50, 120));
            $handed_status = rand(Apartment::STATUS_HANDED, Apartment::STATUS_HANDE);
            $sheet->setCellValue('D' . $i, isset($handed_list[$handed_status]) ? $handed_list[$handed_status] : '');
            $validation = $sheet->getCell('D' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $handed_list_str . '"');
            if($handed_status == Apartment::STATUS_HANDED){
                $sheet->setCellValue('E' . $i, '11/12/2022');
            }else{
                $sheet->setCellValue('E' . $i, '');
            }
            $sheet->setCellValue('F' . $i, rand(2,5));
            $type_list_status = array_rand($type_list);
            $sheet->setCellValue('G' . $i, isset($type_list[$type_list_status]) ? $type_list[$type_list_status] : '');
            $validation = $sheet->getCell('G' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $type_list_str . '"');

            $sheet->setCellValue('H' . $i, 'Khu H');
            $sheet->setCellValue('I' . $i, 'Tòa A');
            $sheet->setCellValue('J' . $i, 'Tầng 0'.($i-1));
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'success' => true,
            'file_path' => $file_path
        ];
    }

    public function export()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $file_path = '/uploads/fee/' . time() . '-apartment-list.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách bất động sản');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên căn hộ');
        $sheet->setCellValue('C1', 'Lô');
        $sheet->setCellValue('D1', 'Tầng');
        $sheet->setCellValue('E1', 'Diện tích');
        $sheet->setCellValue('F1', 'Số thành viên');
        $sheet->setCellValue('G1', 'Mã căn');
        $sheet->setCellValue('H1', 'Loại căn');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFill()->getStartColor()->setARGB('10a54a');
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
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getAlignment()->setWrapText(true);

        $i = 2;
        $apartments = Apartment::find()->where(['building_cluster_id' => $buildingCluster->id, 'is_deleted' => Apartment::NOT_DELETED])->orderBy(['name' => SORT_ASC])->all();
        foreach ($apartments as $apartment) {
            $type_list = $apartment->getFormTypeList();
            $lo = '';
            $tang = '';
            if($apartment->parent_path){
                list($lo, $tang) = explode('/', $apartment->parent_path);
            }
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $apartment->name);
            $sheet->setCellValue('C' . $i, $lo);
            $sheet->setCellValue('D' . $i, $tang);
            $sheet->setCellValue('E' . $i, $apartment->capacity);
            $sheet->setCellValue('F' . $i, $apartment->total_members);
            $sheet->setCellValue('G' . $i, $apartment->code);
            $sheet->setCellValue('H' . $i, isset($type_list[$apartment->form_type]) ? $type_list[$apartment->form_type] : '');
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path
        ];
    }
}
