<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceMapManagement;
use common\models\ServiceOldDebitFee;
use common\models\ServiceWaterInfo;
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
use yii\helpers\Html;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceOldDebitFeeImportForm")
 * )
 */
class ServiceOldDebitFeeImportForm extends Model
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
        $apartmentMapResidentUserApprovedError = [];
        $serviceWaterInfoError = [];
        $arrCreateError = [];
        $arrIndexError = [];
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D', 'E'];
        while (true) {
            $rows = [];
            $stop = 0;
            $setSheet = 'Danh sách nợ cũ';
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
//                    $val = $val . '/';
//                }
                $rows[] = $val;

            }
            // if ($stop == count($arrColumns)) {
            //     break;
            // }
            $i++;
            if (count($arrColumns) > count($rows)) {
                continue;
            }
            $rows[2] = trim($rows[2]);
            $rows[3] = trim($rows[3]);
            $isCheckNameBds = true ;
            if("" == $rows[1])
            {
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Tên bất động sản không được để trống")
                ];
            }else{
                $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['building_cluster_id' => $building_cluster_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'apartment_name' => $rows[1]]);
                if (empty($apartmentMapResidentUser)) {
                    $apartmentMapResidentUserArrayError[] = [
                        'line' => $i - 2,
                        'apartment_name' => $rows[1],
                        'apartment_parent_path' => $rows[2].'/'.$rows[3],
                        'message' => Yii::t('frontend', "Bất động sản không tồn tại trên hệ thống hoặc chưa có chủ hộ")
                    ];
                } else {
                    if ($is_validate == 0) {
                        //lấy ra chỉ số đầu kỳ của apartment
                        $fee_of_month = time();
                        Yii::info($fee_of_month);
                        $rows[4] = str_replace(',', '', $rows[4]);
                        Yii::warning($rows[4]);
                        if(strpos($rows[4],")")>0){
                            $rows[4] = str_replace('(', '', $rows[4]);
                            $rows[4] = str_replace(')', '', $rows[4]);
                            Yii::warning('====');
                            Yii::warning($rows[4]);
                            $rows[4] = -$rows[4];
                            Yii::warning('xxxx');
                            Yii::warning($rows[4]);
                        }
                        if($rows[2] === 0){
                            Yii::error('Số tiền không đúng');
                            $arrCreateError[] = [
                                'line' => $i - 2,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $rows[2].'/'.$rows[3],
                                'errors' => 'Số tiền không đúng',
                                'message' => Yii::t('frontend', "Số tiền không đúng")
                            ];
                            continue;
                        }
                        if(empty($rows[2]))
                        {
                            Yii::error('Tổng tiền không được để trống');
                            $arrCreateError[] = [
                                'line' => $i - 2,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $rows[2].'/'.$rows[3],
                                'errors' => 'Tổng tiền không được để trống',
                                'message' => Yii::t('frontend', "Tổng tiền không được để trống")
                            ];
                            continue;
                        }
                        if(!is_numeric($rows[2]))
                        {
                            Yii::error('Tổng tiền chỉ cho phép nhập ký tự số');
                            $arrCreateError[] = [
                                'line' => $i - 2,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $rows[2].'/'.$rows[3],
                                'errors' => 'Tổng tiền chỉ cho phép nhập ký tự số',
                                'message' => Yii::t('frontend', "Tổng tiền chỉ cho phép nhập ký tự số")
                            ];
                            continue;
                        }
                        if(strlen(round($rows[2])) > 10)
                        {
                            Yii::error('Tổng tiền không hợp lệ');
                            $arrCreateError[] = [
                                'line' => $i - 2,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $rows[2].'/'.$rows[3],
                                'errors' => 'Tổng tiền không hợp lệ',
                                'message' => Yii::t('frontend', "Tổng tiền không hợp lệ")
                            ];
                            continue;
                        }
                        
                        if(strlen($rows[3]) > 1000)
                        {
                            Yii::error('Mô tả cho phép nhập tối đa 1000 ký tự');
                            $arrCreateError[] = [
                                'line' => $i - 2,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $rows[2].'/'.$rows[3],
                                'errors' => 'Mô tả cho phép nhập tối đa 1000 ký tự',
                                'message' => Yii::t('frontend', "Mô tả cho phép nhập tối đa 1000 ký tự")
                            ];
                            continue;
                        }
                        if(strlen($rows[4]) > 1000)
                        {
                            Yii::error('Mô tả (En) cho phép nhập tối đa 1000 ký tự');
                            $arrCreateError[] = [
                                'line' => $i - 2,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $rows[2].'/'.$rows[3],
                                'errors' => 'Mô tả (En) cho phép nhập tối đa 1000 ký tự',
                                'message' => Yii::t('frontend', "Mô tả (En) cho phép nhập tối đa 1000 ký tự")
                            ];
                            continue;
                        }
                        $serviceWaterFee = new ServiceOldDebitFee();
                        $serviceWaterFee->service_map_management_id = $service_map_management_id;
                        $serviceWaterFee->building_cluster_id = $building_cluster_id;
                        $serviceWaterFee->building_area_id = $apartmentMapResidentUser->building_area_id;
                        $serviceWaterFee->apartment_id = $apartmentMapResidentUser->apartment_id;
                        $serviceWaterFee->total_money = round($rows[2]);
                        $serviceWaterFee->description = $rows[4];
                        $serviceWaterFee->description_en = $rows[4];
                        $serviceWaterFee->fee_of_month = $fee_of_month;
                        if (!$serviceWaterFee->save()) {
                            Yii::error($serviceWaterFee->errors);
                            $arrCreateError[] = [
                                'line' => $i - 2,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $rows[2].'/'.$rows[3],
                                'errors' => $serviceWaterFee->errors,
                                'message' => Yii::t('frontend', "Tạo nợ cũ không thành công")
                            ];
                        };
                    }
                    $imported++;
                }
            }
            
        }
        Yii::error([
            'apartmentMapResidentUserArrayError' => $apartmentMapResidentUserArrayError,
            'apartmentMapResidentUserApprovedError' => $apartmentMapResidentUserApprovedError,
            'serviceWaterInfoError' => $serviceWaterInfoError,
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
            'serviceWaterInfoError' => $serviceWaterInfoError,
            'TotalRow' => $i - 2,
            'TotalImport' => $imported,
        ];
    }

    public function genForm()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $file_path = '/uploads/fee/' . time() . '-service-old-debit-fee.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách nợ cũ');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên bất động sản');
        $sheet->setCellValue('C1', 'Tổng tiền');
        $sheet->setCellValue('D1', 'Mô tả');
        $sheet->setCellValue('E1', 'Mô tả (En)');

        $arrColumns = ['A', 'B', 'C', 'D', 'E'];
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFill()->getStartColor()->setARGB('10a54a');
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
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setWrapText(true);

        $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['building_cluster_id' => $building_cluster_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED])->all();
        $i = 2;
        $ids = [];
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser) {
            if($i > 3)
            {
                break;
            }
            if(isset($ids[$apartmentMapResidentUser->apartment_id])){ continue;}
            list($lo, $tang) = explode('/', $apartmentMapResidentUser->apartment_parent_path);
            $ids[$apartmentMapResidentUser->apartment_id] = $apartmentMapResidentUser->apartment_id;
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $apartmentMapResidentUser->apartment_name);
            $sheet->setCellValue('C' . $i, 0);
            $sheet->setCellValue('D' . $i, '');
            $sheet->setCellValue('E' . $i, '');
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

         $sheet2->setCellValue('A3','Tên bất động sản');
         $sheet2->setCellValue('B3','Có');
         $cellValue = "- Giới hạn 10 ký tự bất kỳ\n- Không cho phép nhập có dấu, khoảng trống\n- Tên bất động sản tồn tại trên hệ thống\n- BĐS đã có chủ hộ";
         $sheet2->setCellValue('C3', Html::decode($cellValue));
         $sheet2->getStyle('C3')->getAlignment()->setWrapText(true);
 
         $sheet2->setCellValue('A4','Số tiền');
         $sheet2->setCellValue('B4','Có');
         $cellValue = "- Cho phép người dùng nhập số tiền cần thanh toán\n-Chỉ cho phép gồm 10 ký tự số";
         $sheet2->setCellValue('C4', Html::decode($cellValue));
         $sheet2->getStyle('C4')->getAlignment()->setWrapText(true);
 
         $sheet2->setCellValue('A5','Mô tả');
         $sheet2->setCellValue('B5','Không');
         $sheet2->setCellValue('C5','- Chỉ cho phép tối đa 1000 ký tự');
 
         $sheet2->setCellValue('A6','Mô tả(EN)');
         $sheet2->setCellValue('B6','Không');
         $sheet2->setCellValue('C6','- Chỉ cho phép tối đa 1000 ký tự');
         // set sheet active là sheet 0
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path
        ];
    }
}
