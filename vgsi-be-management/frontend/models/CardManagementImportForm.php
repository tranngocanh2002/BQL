<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingArea;
use common\models\CardManagement;
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
class CardManagementImportForm extends Model
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
        $cardArrayError = [];
        $cardError = [];
        $arrayBuildingAreaEmpty = [];
        $arrCreateError = [];
        $i = 2;
        $arrColumns = ['A', 'B', 'C'];
        while (true) {
            $rows = [];
            $stop = 0;
            $setSheet = 'Danh sách thẻ hợp nhất';
            if ($sheetNames[0] !== $setSheet) {
                $cardArrayError[] = [
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
                if ($col == 'B' && empty($val)) {
                    $val = '';
                }
                if ($col == 'C' && empty($val)) {
                    $val = '';
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

            if (!preg_match('/^[a-zA-Z0-9.\-_]+$/', $rows[1])) {
                $cardArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => '',
                    'message' => Yii::t('frontend', "Mã thẻ không được chứa dấu và khoảng trống")
                ];
                continue;
            }
            if (!preg_match('/^[a-zA-Z0-9.\-_]+$/', $rows[2])) {
                $cardArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => '',
                    'message' => Yii::t('frontend', "Số thẻ không được chứa dấu và khoảng trống")
                ];
                continue;
            }
            if (empty($rows[1])) {
                $cardArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => '',
                    'message' => Yii::t('frontend', "Mã thẻ không được để trống")
                ];
                continue;
            }
            if (empty($rows[2])) {
                $cardArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => '',
                    'message' => Yii::t('frontend', "Số thẻ không được để trống")
                ];
                continue;
            }
            $cardManagement = CardManagement::findOne(['building_cluster_id' => $building_cluster_id, 'code' => $rows[1]]);
            if (!empty($cardManagement)) {
                Yii::error('apartment exist ' . $rows[1]);
                //đã tồn tại
                $cardArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => '',
                    'message' => Yii::t('frontend', "Mã thẻ đã tồn tại:") . " " .$rows[1]
                ];
                $cardArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[2],
                    'apartment_parent_path' => '',
                    'message' => Yii::t('frontend', "Số thẻ đã tồn tại:") . " " .$rows[2]
                ];
            } else {
                if ($is_validate == 0) {
                    $cardManagement = new CardManagement();
                    $cardManagement->code = $rows[1];
                    $cardManagement->building_cluster_id = $building_cluster_id;
                    $cardManagement->number = $rows[2];
                    $cardManagement->status = CardManagement::STATUS_CREATE;
                    if (!$cardManagement->save()) {
                        Yii::error('apartment create error ' . $rows[1]);
                        Yii::error($cardManagement->errors);
                        $message = "Thêm thẻ hợp nhất không thành công: " .$rows[1];
                        if($cardManagement->errors){
                            foreach ($cardManagement->errors as $k => $v){
                                $message = $v[0];
                            }
                        }
                        $arrCreateError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => '',
                            'message' => $message
                        ];
                    }else{
                        $imported++;
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
            'cardArrayError' => $cardArrayError,
            'cardError' => $cardError,
            'ArrayBuildingAreaEmpty' => $arrayBuildingAreaEmpty,
            'arrCreateError' => $arrCreateError,
            'TotalRow' => $i - 2,
            'TotalImport' => $imported,
        ];
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
        $file_path = '/uploads/fee/' . time() . '-cardmanagement.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách thẻ');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Mã thẻ');
        $sheet->setCellValue('C1', 'Tên thẻ');
        $sheet->setCellValue('D1', 'Tên bất động sản');
        $sheet->setCellValue('E1', 'Chủ thẻ');
        $sheet->setCellValue('F1', 'Trạng thái');
        $sheet->setCellValue('G1', 'Người cập nhật');
        $sheet->setCellValue('H1', 'Ngày tạo');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFill()->getStartColor()->setARGB('10a54a');
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
        $spreadsheet->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setWrapText(true);

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
        $file_path = '/uploads/fee/' . time() . '-card-list.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách thẻ hợp nhất');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Mã thẻ');
        $sheet->setCellValue('C1', 'Số thẻ');
        $sheet->setCellValue('D1', 'Trạng thái');
        $sheet->setCellValue('E1', 'BĐS');
        $sheet->setCellValue('F1', 'Chủ thẻ');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F'];
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
        $cardManagements = CardManagement::find()->where(['building_cluster_id' => $buildingCluster->id])->orderBy(['code' => SORT_ASC])->all();
        foreach ($cardManagements as $cardManagement) {
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['id' => $cardManagement->resident_user_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            $apartment = Apartment::findOne(['id' => $cardManagement->apartment_id, 'is_deleted' => Apartment::NOT_DELETED]);
            $type_list = $cardManagement->getFormTypeList();
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $cardManagement->code);
            $sheet->setCellValue('C' . $i, $cardManagement->name);
            $sheet->setCellValue('D' . $i, $cardManagement->status);
            $sheet->setCellValue('E' . $i, $apartment->name);
            $sheet->setCellValue('F' . $i, $apartmentMapResidentUser->resident_user_first_name . ' ' . $apartmentMapResidentUser->resident_user_last_name);
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path
        ];
    }
}
