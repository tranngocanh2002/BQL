<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceMapManagement;
use common\models\ServiceElectricFee;
use common\models\ServiceElectricInfo;
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
 *   @SWG\Xml(name="ServiceElectricFeeImportForm")
 * )
 */
class ServiceElectricFeeImportForm extends Model
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
        $ServiceElectricInfoError = [];
        $arrCreateError = [];
        $arrIndexError = [];
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F'];
        while (true) {
            $rows = [];
            $stop = 0;
            $setSheet = 'Danh sách chốt số điện';
            if ($sheetNames[0] !== $setSheet) {
                $apartmentMapResidentUserApprovedError[] = [
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
            foreach ($arrColumns as $key => $col) {
                $cell = $xls_datas->getCell($col . $i);
                $val = $cell->getFormattedValue();
                $val = trim($val);
                if ($col == 'A' && empty($val)) {
                    $val = '';
                }
                //ngay chot
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
                            $apartmentMapResidentUserApprovedError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày chốt không đúng định dạng")
                            ];
                            break;
                        }
                    }else{
                        $val = null;
                    }
                }
                // phi cua thang
                if($col == 'E'){
                    // if(!empty($val) && !$this->isValidDate($val))
                    // if(empty($val))
                    // {
                    //     $val = 'e';
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
                            $apartmentMapResidentUserApprovedError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Phí của tháng không đúng định dạng")
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
            $rows[2] = trim($rows[2]);
            $rows[3] = trim($rows[3]);
            
            if(empty($rows[2])){
                $apartmentMapResidentUserApprovedError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => "",
                    'message' => Yii::t('frontend', "Ngày chốt không được để trống")
                ];
                continue;
            }
            // if('c' == $rows[2]){
            //     $apartmentMapResidentUserApprovedError[] = [
            //         'line' => $i - 2,
            //         'apartment_name' => $rows[1],
            //         'apartment_parent_path' => "",
            //         'message' => Yii::t('frontend', "Ngày chốt không đúng định dạng")
            //     ];
            //     continue;
            // }
            if("" == $rows[3]){
                $apartmentMapResidentUserApprovedError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Chỉ số chốt không được để trống")
                ];
                continue;
            }
            if(empty($rows[4])){
                $apartmentMapResidentUserApprovedError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Phí của tháng không được để trống")
                ];
                continue;
            }
            // if('e' == $rows[4]){
            //     $apartmentMapResidentUserApprovedError[] = [
            //         'line' => $i - 2,
            //         'apartment_name' => $rows[1],
            //         'apartment_parent_path' => $rows[2].'/'.$rows[3],
            //         'message' => Yii::t('frontend', "Phí của tháng không đúng định dạng")
            //     ];
            //     continue;
            // }
            if("" == $rows[5]){
                $apartmentMapResidentUserApprovedError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Chỉ số tiêu thụ không được để trống")
                ];
                continue;
            }
            if($rows[2] > $rows[4]){
                $apartmentMapResidentUserApprovedError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Ngày chốt hoặc phí của tháng không hợp lệ")
                ];
                continue;
            }
            if(!is_numeric($rows[3]) || (int)$rows[3] < 0){
                $apartmentMapResidentUserApprovedError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Chỉ số chốt không đúng định dạng")
                ];
                continue;
            }
            if(!is_numeric($rows[5]) || $rows[5] < 0){
                $apartmentMapResidentUserApprovedError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Chỉ số tiêu thụ không đúng định dạng")
                ];
                continue;
            }
            if($rows[3] < $rows[5]){
                $apartmentMapResidentUserApprovedError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2].'/'.$rows[3],
                    'message' => Yii::t('frontend', "Chỉ số tiêu thụ phải nhỏ hơn chỉ số chốt")
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
                    //lấy ra chỉ số đầu kỳ của apartment
                    $lock_time = $rows[2];
                    $fee_of_month = $rows[4];
                    Yii::info($lock_time);
                    Yii::info($fee_of_month);

                    $ServiceElectricInfo = ServiceElectricInfo::findOne(['apartment_id' => $apartmentMapResidentUser->apartment_id, 'service_map_management_id' => $service_map_management_id]);
                    if(empty($ServiceElectricInfo)){
                        $ServiceElectricInfoError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'message' => Yii::t('frontend', "Bất động sản chưa khai báo chỉ số sử dụng ban đầu")
                        ];
                        continue;
                    }

                    if($lock_time < $ServiceElectricInfo->tmp_end_date){
                        $ServiceElectricInfoError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'message' => Yii::t('frontend', "Thời gian chốt không phù hợp")
                        ];
                        continue;
                    }

                    if($lock_time > time()){
                        $ServiceElectricInfoError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'message' => Yii::t('frontend', "Thời gian chốt lớn hơn thời gian hiện tại")
                        ];
                        continue;
                    }

                    $start_index = $ServiceElectricInfo->end_index;
                    $start_time = $ServiceElectricInfo->end_date;

                    $ServiceElectric = ServiceElectricFee::find()->where([
                        'service_map_management_id' => $service_map_management_id,
                        'building_cluster_id' => $building_cluster_id,
                        'apartment_id' => $apartmentMapResidentUser->apartment_id,
                    ])->orderBy(['id' => SORT_DESC])->one();
                    if (!empty($ServiceElectric)) {
                        if($ServiceElectric->status == ServiceElectricFee::STATUS_UNACTIVE){
                            $apartmentMapResidentUserApprovedError[] = [
                                'line' => $i - 2,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $rows[2].'/'.$rows[3],
                                'message' => Yii::t('frontend', "Bất động sản còn phí chưa duyệt")
                            ];
                            continue;
                        }
                    }

                    //xử lý chỉ số tiêu thụ nếu có
                    //Các trường hợp phát sinh do thay đổi đồng hồ, chỉ số chốt cuối không còn phù hợp
                    if(isset($rows[5]) && (trim($rows[5]) != '') && $rows[5] >= 0){
                        $start_index = $rows[3] - $rows[5];
                    }

                    if ($start_index >= $rows[3] || ($rows[3] <= 0 && $rows[5] <= 0) || $rows[3] < 0 || (isset($rows[5]) && (trim($rows[5]) != '') && $rows[5] < 0)) {
                        $arrIndexError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2].'/'.$rows[3],
                            'start_index' => $start_index,
                            'end_index' => $rows[3],
                            'message' => Yii::t('frontend', "Chỉ số sử dụng không đúng")
                        ];
                    } else {
                        //                        $ServiceElectricFeeOld = ServiceElectricFee::find()->where(['building_cluster_id' => $building_cluster_id, 'apartment_id' => $apartmentMapResidentUser->apartment_id])->orderBy(['end_index' => SORT_DESC])->one();
                        //                        if (!empty($ServiceElectricFeeOld)) {
                        //                            $start_time = $ServiceElectricFeeOld->lock_time;
                        //                        } else {
                        //                            $start_time = strtotime(date('Y-m-01', $fee_of_month));
                        //                        }

                        $ServiceElectricFee = new ServiceElectricFee();
                        $ServiceElectricFee->service_map_management_id = $service_map_management_id;
                        $ServiceElectricFee->building_cluster_id = $building_cluster_id;
                        $ServiceElectricFee->building_area_id = $apartmentMapResidentUser->building_area_id;
                        $ServiceElectricFee->apartment_id = $apartmentMapResidentUser->apartment_id;
                        $ServiceElectricFee->start_index = $start_index;
                        $ServiceElectricFee->end_index = $rows[3];
                        $ServiceElectricFee->total_index = $rows[3] - $start_index;
                        $genchage = $ServiceElectricFee->getCharge($building_cluster_id, $service_map_management_id, $rows[3], $start_index, $lock_time,$apartmentMapResidentUser->apartment_id);
                        $ServiceElectricFee->total_money = $genchage['total_money'];
                        if($ServiceElectricFee->total_money <= 0){
                            $arrCreateError[] = [
                                'line' => $i - 2,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $rows[2].'/'.$rows[3],
                                'message' => Yii::t('frontend', "Tổng tiền phí không phù hợp: ". $genchage['total_money'])
                            ];
                            continue;
                        }
                        $ServiceElectricFee->description = $genchage['description'];
                        $ServiceElectricFee->json_desc = json_encode($genchage['json_desc']);
                        $ServiceElectricFee->start_time = $start_time;
                        $ServiceElectricFee->lock_time = $lock_time;
                        $ServiceElectricFee->fee_of_month = $fee_of_month;
                        if (!$ServiceElectricFee->save()) {
                            Yii::error($ServiceElectricFee->errors);
                            $arrCreateError[] = [
                                'line' => $i - 2,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => $rows[2].'/'.$rows[3],
                                'errors' => $ServiceElectricFee->errors,
                                'message' => Yii::t('frontend', "Chỉ số chốt phải là số nguyên")
                            ];
                        }else{
                            $imported++;
                        };
                    }
                }
            }
        }
        Yii::error([
            'apartmentMapResidentUserArrayError' => $apartmentMapResidentUserArrayError,
            'apartmentMapResidentUserApprovedError' => $apartmentMapResidentUserApprovedError,
            'ServiceElectricInfoError' => $ServiceElectricInfoError,
            'arrCreateError' => $arrCreateError,
            'arrIndexError' => $arrIndexError,
        ]);
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
            'apartmentMapResidentUserApprovedError' => $apartmentMapResidentUserApprovedError,
            'ServiceElectricInfoError' => $ServiceElectricInfoError,
            'arrCreateError' => $arrCreateError,
            'arrIndexError' => $arrIndexError,
            'TotalRow' => $i - 2,
            'TotalImport' => $imported,
        ];
    }

    public function genForm()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $file_path = '/uploads/fee/'.time().'-service-electric-fee.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách chốt số điện');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên bất động sản');
        $sheet->setCellValue('C1', 'Ngày chốt');
        $sheet->setCellValue('D1', 'Chỉ số chốt');
        $sheet->setCellValue('E1', 'Phí của tháng');
        $sheet->setCellValue('F1', 'Chỉ số tiêu thụ');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F'];
        $spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFill()->getStartColor()->setARGB('10a54a');
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
        $spreadsheet->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setWrapText(true);

        $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['building_cluster_id' => $building_cluster_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD])->all();
        $i = 2;
        $ids = [];
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser){
            if($i >= 3)
            {
                break;
            }
            if(isset($ids[$apartmentMapResidentUser->apartment_id])){ continue;}
            list($lo, $tang) = explode('/', $apartmentMapResidentUser->apartment_parent_path);
            $ids[$apartmentMapResidentUser->apartment_id] = $apartmentMapResidentUser->apartment_id;
            $sheet->setCellValue('A'.$i, $i-1);
            $sheet->setCellValue('B'.$i, $apartmentMapResidentUser->apartment_name);
//            $sheet->setCellValue('D'.$i, "'".date('d/m/Y'));
//            $sheet->setCellValue('E'.$i, 0);
//            $sheet->setCellValue('F'.$i, "'".date('d/m/Y'));
            //            $sheet->setCellValue('D'.$i, "'".date('d/m/Y'));
            $sheet->setCellValue('C'.$i, date('d/m/Y'));
            $sheet->getStyle('C'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
            $sheet->setCellValue('D'.$i, 100);
//            $sheet->setCellValue('F'.$i, "'".date('d/m/Y'));
            $sheet->setCellValue('E'.$i, date('d/m/Y'));
            $sheet->getStyle('E'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
            $sheet->setCellValue('F'.$i, 50);
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
 
         $sheet2->setCellValue('A4','Ngày chốt');
         $sheet2->setCellValue('B4','Có');
         $sheet2->setCellValue('C4','- Định dạng: dd/mm/yyyy');
 
         $sheet2->setCellValue('A5','Chỉ số chốt');
         $sheet2->setCellValue('B5','Có');
         $sheet2->setCellValue('C5','-Chỉ cho phép nhập số ');

         $sheet2->setCellValue('A6','Phí của tháng');
         $sheet2->setCellValue('B6','Có');
         $cellValue = "- Định dạng: dd/mm/yyyy\n-Phí của tháng phải lớn hơn ngày chốt";
         $sheet2->setCellValue('C6', Html::decode($cellValue));
         $sheet2->getStyle('C6')->getAlignment()->setWrapText(true);

         $sheet2->setCellValue('A7','Chỉ số tiêu thụ');
         $sheet2->setCellValue('B7','Có');
         $cellValue = "- Chỉ cho phép nhập số\n-Nhỏ hơn chỉ số chốt";
         $sheet2->setCellValue('C7', Html::decode($cellValue));
         $sheet2->getStyle('C7')->getAlignment()->setWrapText(true);

         // set sheet active là sheet 0
        $spreadsheet->setActiveSheetIndex(0);
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path
        ];
    }

    public function isValidDate($date) {
        return (strtotime($date) !== false);
    }

}
