<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingArea;
use common\models\ManagementUser;
use common\models\rbac\AuthGroup;
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
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ManagementUserImportForm")
 * )
 */
class ManagementUserImportForm extends Model
{
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
            [['file_path'], 'required'],
            [['file_path'], 'string'],
        ];
    }


    public function import()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;

        $fileandpath = \Yii::getAlias('@webroot') . $this->file_path;
        $spreadsheet = IOFactory::load($fileandpath);
        $xls_datas = $spreadsheet->getActiveSheet();
        $sheetNames = $spreadsheet->getSheetNames();

        $imported = 0;
        $apartmentArrayError = [];
        $ApartmentError = [];
        $arrCreateError = [];
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G','H'];
        while (true) {
            $rows = [];
            $stop = 0;
            $setSheet = 'Danh sách nhân sự';
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
                if($col == 'E'){
                    if(empty($val)){
                        $apartmentArrayError[] = [
                            'line' => $i - 1,
                            'management_user_name' => $rows[2],
                            'message' => Yii::t('frontend', "Ngày sinh không được để trống")
                        ];
                        continue;
                    }
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
                                'management_user_name' => $rows[2],
                                'message' => Yii::t('frontend', "Ngày sinh không đúng định dạng")
                            ];
                            continue;
                        }
                        if($val > time()){
                            $apartmentArrayError[] = [
                                'line' => $i - 1,
                                'management_user_name' => $rows[2],
                                'message' => Yii::t('frontend', "Ngày sinh không hợp lệ")
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
            if (count($arrColumns) > count($rows)) {
                continue;
            }
            if(strlen($rows[2]) > 50){
                //đã tồn tại
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'management_user_name' => $rows[2],
                    'message' => Yii::t('frontend', "Họ và tên cho phép tối đa 50 ký tự")
                ];
                continue;
            }
            if(empty($rows[1])){
                Yii::error('managementUser exist ' . $rows[1]);
                //đã tồn tại
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'management_user_name' => $rows[1],
                    'message' => Yii::t('frontend', "Mã nhân viên không được để trống")
                ];
                continue;
            }
            if(strlen($rows[1]) > 10){
                //đã tồn tại
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'management_user_name' => $rows[1],
                    'message' => Yii::t('common', "Mã nhân viên cho phép tối đa 10 ký tự")
                ];
                continue;
            }
            if(empty($rows[2])){
                Yii::error('managementUser exist ' . $rows[2]);
                //đã tồn tại
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'management_user_name' => $rows[2],
                    'message' => Yii::t('frontend', "Họ và tên không được bỏ trống")
                ];
                continue;
            }
            if (preg_match('/\d|[@#$%^&*()\-+!.,\';:"`~=<>?|]/', $rows[2])) {
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'management_user_name' => $rows[2],
                    'message' => Yii::t('frontend', "Họ và tên chỉ được nhập chữ")
                ];
                continue;
            }
            
            if($rows[4] > time()){
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'management_user_name' => $rows[2],
                    'message' => Yii::t('frontend', "Ngày sinh không hợp lệ")
                ];
                continue;
            }
            if(empty($rows[5])){
                Yii::error('managementUser exist ' . $rows[5]);
                //đã tồn tại
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'management_user_name' => $rows[2],
                    'message' => Yii::t('frontend', "Email không được bỏ trống")
                ];
                continue;
            }else if (strlen($rows[5]) > 50){
                //đã tồn tại
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'management_user_name' => $rows[2],
                    'message' => Yii::t('frontend', "Email cho phép tối đa 50 ký tự")
                ];
                continue;
            }
            if(empty($rows[6])){
                Yii::error('managementUser exist ' . $rows[6]);
                //đã tồn tại
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'management_user_name' => $rows[2],
                    'message' => Yii::t('frontend', "Số điện thoại không được bỏ trống")
                ];
                continue;
            }
            $phone = CUtils::validateMsisdn($rows[6]);
            if(!empty($phone)){
                $managementUserCheckPhone = ManagementUser::findOne(['building_cluster_id' => $building_cluster_id, 'phone' => $phone, 'is_deleted' => Apartment::NOT_DELETED]);
                if(!empty($managementUserCheckPhone)){
                    Yii::error('managementUser exist ' . $rows[6]);
                    //đã tồn tại
                    $apartmentArrayError[] = [
                        'line' => $i - 2,
                        'management_user_name' => $rows[2],
                        'message' => Yii::t('frontend', "Số điện thoại đã tồn tại trên hệ thống")
                    ];
                    continue;
                }
            }
            $managementUserCode = ManagementUser::findOne(['building_cluster_id' => $building_cluster_id, 'code_management_user' => $rows[1], 'is_deleted' => Apartment::NOT_DELETED]);
            if (!empty($managementUserCode)) {
                Yii::error('Code management user exist ' . $rows[1]);
                //đã tồn tại
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'management_user_name' => $rows[1],
                    'message' => Yii::t('frontend', "Mã nhân viên đã tồn tại:" . " " .$rows[1] )
                ];
                continue;
            }
            $managementUser = ManagementUser::findOne(['email' => $rows[5], 'is_deleted' => Apartment::NOT_DELETED]);
            if (!empty($managementUser)) {
                Yii::error('managementUser exist ' . $rows[5]);
                //đã tồn tại
                $apartmentArrayError[] = [
                    'line' => $i - 2,
                    'management_user_name' => $rows[5],
                    'message' => Yii::t('frontend', "Email đã tồn tại trong hệ thống: " .$rows[5] )
                ];
            } else {
                $gender = ManagementUser::GENDER_0;
                if($rows[3] == 'Nam'){
                    $gender = ManagementUser::GENDER_1;
                }else if($rows[3] == 'Nữ'){
                    $gender = ManagementUser::GENDER_2;
                }
                $managementUser = new ManagementUser();
                $managementUser->status = ManagementUser::STATUS_ACTIVE;
                $managementUser->first_name = $rows[2];
                $managementUser->gender = $gender;
                if (!empty($rows[4])) {
                    $managementUser->birthday = $rows[4];
                }

                $managementUser->phone = $phone;
                if(!empty($rows[6]) && empty($phone)){
                    Yii::error('Số điện thoại không hợp lệ: ' . $rows[6]);
                    $arrCreateError[] = [
                        'line' => $i - 2,
                        'management_user_name' => $rows[2],
                        'message' => Yii::t('frontend', 'Số điện thoại không hợp lệ' ),
                        'errors' => $managementUser->errors
                    ];
                    continue;
                }
                if (!empty($rows[7])) {
                    $authGroup = AuthGroup::findOne(['name' => $rows[7], 'building_cluster_id' => $building_cluster_id]);
                    if (!is_null($authGroup)) {
                        $managementUser->auth_group_id = $authGroup->id;
                    } else {
                        Yii::error('Nhóm quyền không tồn tại: ' . $rows[7]);
                        $arrCreateError[] = [
                            'line' => $i - 2,
                            'management_user_name' => $rows[2],
                            'message' => Yii::t('frontend', "Nhóm quyền không tồn tại: " .$rows[7] ),
                            'errors' => $managementUser->errors
                        ];
                        continue;
                    }
                }
                $managementUser->email = $rows[5];
                $managementUser->code_management_user = $rows[1];
                $managementUser->building_cluster_id = $building_cluster_id;
                $password = CUtils::randomString(10);
                $managementUser->setPassword($password);
                if (!$managementUser->save()) {
                    Yii::error('apartment create error ' . $rows[2]);
                    Yii::error($managementUser->errors);
                    $message = "Thêm nhân sự không thành công: " .$rows[5];
                    if($managementUser->errors){
                        foreach ($managementUser->errors as $k => $v){
                            $message = $v[0];
                        }
                    }
                    $arrCreateError[] = [
                        'line' => $i - 2,
                        'management_user_name' => $rows[2],
                        'message' => Yii::t('frontend', $message),
                    ];
                } else {
                    $imported++;
                    $managementUser->sendEmailCreatePassword($password);
                }
            }
        }
        $success = true;
        $message = Yii::t('frontend', "Import success");
        if ($imported <= 0) {
            $message = Yii::t('frontend', "Import Error");
        }
        return [
            'success' => $success,
            'message' => $message,
            'managementUserArrayError' => $apartmentArrayError,
            'managementUserError' => $ApartmentError,
            'arrCreateError' => $arrCreateError,
            'TotalRow' => $i - 2,
            'TotalImport' => $imported,
        ];
    }

    public function genForm()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $file_path = '/uploads/fee/' . time() . '-management-user.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách nhân sự');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Mã nhân viên');
        $sheet->setCellValue('C1', 'Họ và Tên');
        $sheet->setCellValue('D1', 'Giới tính');
        $sheet->setCellValue('E1', 'Ngày sinh');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Số điện thoại');
        $sheet->setCellValue('H1', 'Nhóm quyền');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G','H'];
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

        $i = 2;
        $authGroups = ArrayHelper::map(AuthGroup::find()->where(['building_cluster_id' => $buildingCluster->id])->all(), 'id', 'name');
        $auth_group_str = implode(',', $authGroups);
        $gender_list_str = implode(',', ManagementUser::$gender_list);
        foreach ($authGroups as $authGroup){
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' .$i, '123XYZ');
            $sheet->setCellValue('C' . $i, trim('Nguyễn Văn A'));
            $gender = array_rand(ManagementUser::$gender_list);
            $sheet->setCellValue('D' . $i, isset(ManagementUser::$gender_list[$gender]) ? ManagementUser::$gender_list[$gender] : '');
            $validation = $sheet->getCell('D' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $gender_list_str . '"');
            $sheet->setCellValue('E' . $i, date('d/m/Y'));
            $sheet->setCellValue('F' . $i, 'demo'.$i.'@luci.vn');
            $sheet->setCellValue('G' . $i, '0988xxxx0'.$i);
            $auth_group_id = array_rand($authGroups);
            $sheet->setCellValue('H' . $i, isset($authGroups[$auth_group_id]) ? $authGroups[$auth_group_id] : '');
            $validation = $sheet->getCell('H' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $auth_group_str . '"');
            $i++;
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
        $sheet->setTitle('Danh sách nhân sự');
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
