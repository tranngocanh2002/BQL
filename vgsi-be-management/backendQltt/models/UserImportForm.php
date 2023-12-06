<?php

namespace backendQltt\models;

use common\helpers\CUtils;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use common\models\User;
use common\models\UserRole;
use yii\helpers\ArrayHelper;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use backendQltt\models\LoggerUser;
use backendQltt\models\LogBehavior;

class UserImportForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $file;
    public $path_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => '.xlsx, .xls'],
            [['path_name'], 'string']
        ];
    }

    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            // TimestampBehavior::className(),
            // LogBehavior::className(),
            'log' => [
                'class' => LogBehavior::class,
            ],
        ];
    }

    public function upload()
    {
        $file = $this->file;
        $uploadDirectory = 'uploads/excel/';
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }
        $fileName = $file['name'];
        $fileTmpPath = $file['tmp_name'];
        $fileError = $file['error'];
        if ($fileError === UPLOAD_ERR_OK) {
            // Di chuyển tệp tin tạm thời đến đường dẫn lưu trữ
            $destination = $uploadDirectory . time().'-' . CUtils::slugify($fileName);
            move_uploaded_file($fileTmpPath, $destination);
            $this->path_name = $destination;
            return true;
        } else {
            return false;
        }
    }

    public function import()
    {
        $fileAndPath = Yii::getAlias('@webroot') . '/' . $this->path_name;
        $spreadSheet = IOFactory::load($fileAndPath);
        $xlsData = $spreadSheet->getActiveSheet();
        $imported = 0;
        $apartmentArrayError = [];
        $ApartmentError = [];
        $arrCreateError = [];
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G','H'];
        
        while (true) {
            $rows = [];
            $stop = 0;
            foreach ($arrColumns as $col) {
                $cell = $xlsData->getCell($col . $i);
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
                $cell = $xlsData->getCell($col . $i);
                $val = $cell->getFormattedValue();
                $val = trim($val);

                if ($col == 'A' && empty($val)) {
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

            $userCode = User::findOne([
                'code_user' => $rows[1],
                'status' => User::STATUS_ACTIVE,
            ]);
            if (!empty($userCode)) {
                Yii::error('user exist ' . $rows[1]);
                //đã tồn tại
                $arrCreateError[] = [
                    'line' => $i - 2,
                    'message' => Yii::t('backendQltt', "Mã nhân viên đã tồn tại")
                ];
                continue;
            }

            $user = User::findOne([
                'email' => $rows[5],
                'status' => User::STATUS_ACTIVE,
            ]);

            if (!empty($user)) {
                Yii::error('user exist ' . $rows[5]);
                //đã tồn tại
                $arrCreateError[] = [
                    'line' => $i - 2,
                    'message' => Yii::t('backendQltt', "Người dùng đã tồn tại")
                ];
                continue;
            } else {
                $gender = User::MALE;
                if ( $rows[3] == 'Nữ') {
                    $gender = User::FEMALE;
                }
                $user = new User();
                $user->full_name = $rows[2];
                $user->sex = $gender;

                if (!empty($rows[4])) {
                    if ($this->_date_is_valid($rows[4]) && strtotime($rows[4]) < \DateTime::createFromFormat('d/m/Y', date('d/m/Y'))->getTimestamp()) {
                        $user->birthday = CUtils::convertStringToTimeStamp($rows[4], 'd/m/Y');
                    } else {
                        $arrCreateError[] = [
                            'line' => $i - 2,
                            'user_name' => $rows[2],
                            'message' => Yii::t('backendQltt', 'Birthday invalid'),
                            'errors' => $user->errors
                        ];

                        continue;
                    }
                }
                if($rows[4] > time()){
                    $arrCreateError[] = [
                        'line' => $i - 2,
                        'user_name' => $rows[2],
                        'message' => Yii::t('frontend', "Ngày sinh không hợp lệ")
                    ];
                    continue;
                }

                $user->phone = $rows[6];
                if(!empty($rows[7])){
                    $userRole = UserRole::findOne(['name' => $rows[7]]);
                    if (!empty($userRole)) {
                        $user->role_id = $userRole->id;
                    } else {
                        $userRole = new UserRole();
                        $userRole->name = $rows[7];
                        $userRole->permission = 'null';
                        if ($userRole->save()) {
                            $user->role_id = $userRole->id;
                        }
                    }
                }
                $user->email = $user->username = $rows[5];
                $user->code_user = $rows[1];
                $user->password = $user->setPassword(12345678);

                if (!$user->save()) {
                    Yii::error('apartment create error ' . $rows[2]);
                    Yii::error($user->errors);
                    $messageError = '';
                    $errors = $user->errors;
                    foreach ($errors as $attribute => $errorMessages) {
                        foreach ($errorMessages as $errorMessage) {
                            $messageError = Yii::t('backendQltt', $errorMessage);
                            break;
                        }
                        break;
                    }
                
                    $arrCreateError[] = [
                        'line' => $i - 2,
                        'user_name' => $rows[2],
                        'message' => Yii::t('backend', $messageError),
                        'errors' => $user->errors
                    ];
                    continue;
                } else {
                    $imported++;
                    $user->sendEmailCreatePassword(12345678, 'Tài khoản truy cập Web QLTT');
                }
            }
        }
        $success = true;
        $message = Yii::t('backend', "Import success");
        if ($imported <= 0) {
            $success = false;
            $message = Yii::t('backend', "Import Error");
        } 

        return [
            'status' => $success,
            'message' => $message,
            'userArrayError' => $apartmentArrayError,
            'userError' => $ApartmentError,
            'arrCreateError' => $arrCreateError,
            'TotalRow' => $i - 2,
            'TotalImport' => $imported,
        ];
    }

    /**
     * @param $dateString
     * 
     * @return boolen
     */
    function _date_is_valid($dateString) {
        $format = 'd/m/Y';
        $dateTime = \DateTime::createFromFormat($format, $dateString);
        $errors = \DateTime::getLastErrors();

        return $dateTime !== false && $errors['warning_count'] === 0 && $errors['error_count'] === 0;
    }

    public function export()
    {
        $filePath = '/uploads/excel/' . time() . '-user-list.xlsx';
        $fileAndPath = Yii::getAlias('@webroot') . $filePath;
        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Mã nhân viên');
        $sheet->setCellValue('C1', 'Họ và tên');
        $sheet->setCellValue('D1', 'Giới tính');
        $sheet->setCellValue('E1', 'Ngày sinh');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Số điện thoại');
        $sheet->setCellValue('H1', 'Nhóm quyền');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G','H'];
        $spreadSheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFont()->setBold(true);
        $spreadSheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadSheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFill()->getStartColor()->setARGB('10a54a');
        foreach ($arrColumns as $column){
            $w = 25;
            if($column == 'A'){
                $w = 10;
            }
            $spreadSheet->getActiveSheet()->getColumnDimension($column)->setWidth($w);
            $spreadSheet->getActiveSheet()->getStyle($column.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadSheet->getActiveSheet()->getStyle($column.'1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }
        $spreadSheet->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        $spreadSheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getAlignment()->setWrapText(true);

        $i = 2;
        $users = User::find()->where(['NOT', ['id' => [1]]])->orderBy(['created_at' => SORT_DESC])->all();
        foreach ($users as $user) {
            // loại bỏ user có quyền root khỏi file export
            if(1 == (int)$user->userRole->id)
            {
                continue;
            }
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $user->code_user);
            $sheet->setCellValue('C' . $i, $user->full_name);
            $sheet->setCellValue('D' . $i, isset($user->sex) ? User::$sex[$user->sex] : '');
            $sheet->setCellValue('E' . $i, $user->birthday ? date('d/m/Y', $user->birthday) : '');
            $sheet->setCellValue('F' . $i, $user->email);
            $phone = preg_replace("/^84/", '0', $user->phone);
            if ($phone[0] === '0') {
                $phone = '84' . substr($phone, 1); // Cắt bỏ số 0 và nối số 84 vào đầu chuỗi
            }
            $sheet->setCellValue('G' . $i, $phone);
            $sheet->setCellValue('H' . $i, $user->userRole->name);
            $i++;
        }
        $writer = new Xlsx($spreadSheet);
        $writer->save($fileAndPath);

        return $fileAndPath;
    }

    public function exportTemplate()
    {
        $filePath = '/uploads/excel/' . time() . '-user-template.xlsx';
        $fileAndPath = Yii::getAlias('@webroot') . $filePath;
        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Mã nhân viên');
        $sheet->setCellValue('C1', 'Họ và tên');
        $sheet->setCellValue('D1', 'Giới tính');
        $sheet->setCellValue('E1', 'Ngày sinh');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Số điện thoại');
        $sheet->setCellValue('H1', 'Nhóm quyền');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'H'];
        $spreadSheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFont()->setBold(true);
        $spreadSheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadSheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFill()->getStartColor()->setARGB('10a54a');
        foreach ($arrColumns as $column){
            $w = 25;
            if($column == 'A'){
                $w = 10;
            }
            $spreadSheet->getActiveSheet()->getColumnDimension($column)->setWidth($w);
            $spreadSheet->getActiveSheet()->getStyle($column.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadSheet->getActiveSheet()->getStyle($column.'1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }
        $spreadSheet->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        $spreadSheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getAlignment()->setWrapText(true);

        $userRoles = UserRole::find()->where(['<>','id','1'])->all();
        $userRoles = ArrayHelper::map($userRoles, 'id', 'name');
        for ($i = 2; $i < 5; $i++) {
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, '123XYZ');
            $sheet->setCellValue('C' . $i, 'Nguyễn Thị A');
            $validation = $sheet->getCell('D' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . implode(',', User::$sex) . '"');

            $sheet->setCellValue('E' . $i, date('d/m/Y'));
            $sheet->setCellValue('F' . $i, 'email'.$i.'@gmail.com');
            $sheet->setCellValue('G' . $i, '84981xxxx0'.$i);
            $validation = $sheet->getCell('H' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . implode(',', array_values($userRoles)) . '"');
        }
        $writer = new Xlsx($spreadSheet);
        $writer->save($fileAndPath);

        return $fileAndPath;
    }
}