<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceBuildingConfig;
use common\models\ResidentUser;
use common\models\ServiceMapManagement;
use common\models\ServiceParkingLevel;
use common\models\ServiceWaterFee;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ResidentUserImportForm")
 * )
 */
class ResidentUserImportForm extends Model
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
        $ApartmentArrayError = [];
        $ResidentUserExist = [];
        $ResidentUserCreateError = [];
        $PhoneErrors = [];
        $ApartmentMapResidentUserCreateError = [];
        $ApartmentMapResidentUserError = [];
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J','K','L','M','N','O'];
        $check_phone_null = true;
        while (true) {
            $rows = [];
            $stop = 0;
            $setSheet = 'Danh sách cư dân';
            if ($sheetNames[0] !== $setSheet) {
                $ApartmentArrayError[] = [
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
                if ($col == 'C' && !empty($val)) {
                    $check_phone_null = false;
                    if (!ctype_digit($val)){
                        $PhoneErrors[] = [
                            'line' => $i - 1,
                            'phone' => $val,
                            'message' => Yii::t('frontend', "Số điện thoại chỉ cho phép nhập số" )
                        ];
                        break;
                    }
                    $col_new = CUtils::validateMsisdn(preg_replace('/[^0-9]/', '', $val));
                    if(!empty($val) && empty($col_new)){
                        $PhoneErrors[] = [
                            'line' => $i - 1,
                            'phone' => $val,
                            'message' => Yii::t('frontend', "Số điện thoại không hợp lệ" )
                        ];
                        break;
                    }else if (empty($col_new)) {
                        $PhoneErrors[] = [
                            'line' => $i - 1,
                            'phone' => $val,
                            'message' => Yii::t('frontend', "Số điện thoại không được để trống" )
                        ];
                        break;
                    }

                    $val = $col_new;
                }
                if ($col == 'E') {
                    if(empty($val)){
                        $val = 7;
                    }else{
                        $val = ApartmentMapResidentUser::$type_list_text[$val] ?? 7;
                    }
                }else if ($col == 'F') {
                    if(empty($val)){
                        $val = -2;
                    }else{
                        $val = ApartmentMapResidentUser::$type_relationship_list_text[$val] ?? -1;
                    }
                }else if ($col == 'G') {
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
                            $ApartmentArrayError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày nhận nhà không đúng định dạng")
                            ];
                            break;
                        }
                    }else{
                        $val = null;
                    }
                }else if ($col == 'I') {
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
                            $ApartmentArrayError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày sinh không đúng định dạng")
                            ];
                            break;
                        }else if($val > time()){
                            $ApartmentArrayError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày sinh không hợp lệ")
                            ];
                            break;
                        }
                    }else{
                        $val = null;
                    }
                }
                if ($col == 'J') {
                    if ($val == 'Nam') {
                        $val = 1;
                    } else if ($val == 'Nữ') {
                        $val = 2;
                    } else {
                        $val = 0;
                    }
                }
                if ($col == 'K') 
                {
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
                            $ApartmentArrayError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày đăng ký tạm trú không đúng định dạng")
                            ];
                            break;
                        }
                        if($val > time()){
                            $ApartmentArrayError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày đăng ký tạm trú không hợp lệ")
                            ];
                            break;
                        }
                    }
                    else
                    {
                        $val = null;
                    }
                }
                if ($col == 'L') 
                {
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
                            $ApartmentArrayError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày nhập khẩu không đúng định dạng")
                            ];
                            break;
                        }
                        if($val > time()){
                            $ApartmentArrayError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày nhập khẩu không hợp lệ")
                            ];
                            break;
                        }
                    }
                    else
                    {
                        $val = null;
                    }
                }
                if ($col == 'N') 
                {
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
                            $ApartmentArrayError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày cấp không đúng định dạng")
                            ];
                            break;
                        }
                        if($val > time()){
                            $ApartmentArrayError[] = [
                                'line' => $i - 1,
                                'apartment_name' => $rows[1],
                                'apartment_parent_path' => '',
                                'message' => Yii::t('frontend', "Ngày cấp không hợp lệ")
                            ];
                            break;
                        }
                    }
                    else
                    {
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
            $apartment_parent_path =  '';

            if (empty($rows[1])) {
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Tên bất động sản không được để trống")
                ];
                continue;
            }

            if (!preg_match('/^[a-zA-Z0-9.\-_]+$/', $rows[1])) {
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Tên bất động sản không được chưa dấu và khoảng trống")
                ];
                continue;
            }
            if (strlen($rows[1]) > 10) {
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Tên bất động sản cho phép tối đa 10 ký tự")
                ];
                continue;
            }
            if (empty($rows[3])) {
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Họ và tên không được để trống")
                ];
                continue;
            }
            if (strlen($rows[3]) > 50) {
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Họ và tên cho phép tối đa 50 ký tự")
                ];
                continue;
            }

            if (preg_match('/\d|[@#$%^&*()\-+!.,\';:"`~=<>?|]/', $rows[3])) {
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Họ và tên chỉ được nhập chữ")
                ];
                continue;
            }

            $apartment = Apartment::findOne(['building_cluster_id' => $building_cluster_id, 'name' => $rows[1], 'is_deleted' => Apartment::NOT_DELETED]);
            if (empty($apartment)) {
                Yii::error('apartment empty' . $rows[1]);
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Tên bất động sản không tồn tại: " .$rows[1] )
                ];
                continue;
            }

            if($check_phone_null == true && empty($rows[2])){
                $PhoneErrors[] = [
                    'line' => $i - 1,
                    'message' => Yii::t('frontend', "Số điện thoại không được để trống" )
                ];
                continue;
            }else if(empty($rows[2])){
                $PhoneErrors[] = [
                    'line' => $i - 1,
                    'phone' => $rows[2],
                    'message' => Yii::t('frontend', "Số điện thoại không hợp lệ" )
                ];
                continue;
            }
            if($rows[4] === -2){
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Vai trò không được để trống" )
                ];
                continue;
            }else if($rows[4] === -1){
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Vai trò hợp lệ" )
                ];
                continue;
            }
            if($rows[5] === -2){
                if($rows[4] == ApartmentMapResidentUser::TYPE_MEMBER_GUEST){
                    $ApartmentArrayError[] = [
                        'line' => $i - 2,
                        'apartment_name' => $rows[1],
                        'apartment_parent_path' => $apartment_parent_path,
                        'message' => Yii::t('frontend', "Mối quan hệ không được để trống" )
                    ];
                    continue;
                } else if($rows[4] == ApartmentMapResidentUser::TYPE_MEMBER){
                    $ApartmentArrayError[] = [
                        'line' => $i - 2,
                        'apartment_name' => $rows[1],
                        'apartment_parent_path' => $apartment_parent_path,
                        'message' => Yii::t('frontend', "Mối quan hệ không được để trống" )
                    ];
                    continue;
                }
            } else if($rows[5] === -1){
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Mối quan hệ không hợp lệ" )
                ];
                continue;
            }
            if($rows[4] == ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD){
                $rows[5] = ApartmentMapResidentUser::TYPE_RELATIONSHIP_HEAD_OF_HOUSEHOLD;
            }
            if($rows[4] == ApartmentMapResidentUser::TYPE_GUEST){
                $rows[5] = ApartmentMapResidentUser::TYPE_RELATIONSHIP_HEAD_OF_HOUSEHOLD;
            }
            if(empty($rows[7])){
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Email không được để trống")
                ];
                continue;
            }
            if(empty($rows[8])){
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Ngày sinh không được để trống")
                ];
                continue;
            }
            if(empty($rows[9])){
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Giới tính không được để trống")
                ];
                continue;
            }
            if(empty($rows[10])){
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Ngày đăng ký tạm trú không được để trống")
                ];
                continue;
            }
            if(empty($rows[11])){
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Ngày nhập khẩu không được để trống")
                ];
                continue;
            }
            if(empty($rows[12])){
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Số CMND/CCCD/ Hộ chiếu không được để trống")
                ];
                continue;
            }
            if (preg_match('/[a-z][A-Z]|[@#$%^&*()\-+!.,\';:"`~=<>?|]/', $rows[12])) {
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Số CMND/CCCD/ Hộ chiếu không đúng định dạng" )
                ];
                continue;
            }
            if(strlen($rows[12]) > 20){
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Số CMND/CCCD/ Hộ chiếu cho phép nhập tối đa 20 ký tự")
                ];
                continue;
            }
            if(empty($rows[13])){
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Ngày cấp không được để trống")
                ];
                continue;
            }
            if($rows[13] > time()){
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Ngày cấp không hợp lệ")
                ];
                continue;
            }
            if(empty($rows[14])){
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Nơi cấp không được để trống")
                ];
                continue;
            }
            $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
            if (!preg_match($pattern, $rows[7])) {
                $ApartmentArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $apartment_parent_path,
                    'message' => Yii::t('frontend', "Email không đúng định dạng" )
                ];
                continue;
            }
            if ($is_validate == 0) {
                $residentUser = ResidentUser::findByPhone($rows[2]);
                $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $apartment->id, 'resident_user_phone' => $rows[2], 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
                if (!empty($apartmentMapResidentUser)) {
                    Yii::error('ApartmentMapResidentUserError' . $rows[1]);
                    $ApartmentMapResidentUserError[] = [
                        'line' => $i - 2,
                        'apartment_name' => $rows[1],
                        'apartment_parent_path' => $apartment_parent_path,
                        'message' => Yii::t('frontend', "Cư dân đã là thành viên của bất động sản" )
                    ];
                    continue;
                }

                $apartmentMapResidentUser = new ApartmentMapResidentUser();
                $apartmentMapResidentUser->apartment_id = $apartment->id;
                $apartmentMapResidentUser->apartment_name = $apartment->name;
                $apartmentMapResidentUser->apartment_capacity = (int)$apartment->capacity;
                $apartmentMapResidentUser->apartment_code = $apartment->code;
                $apartmentMapResidentUser->apartment_parent_path = $apartment->parent_path;
                $apartmentMapResidentUser->resident_user_id = $residentUser ? $residentUser->id : null;
                $apartmentMapResidentUser->resident_user_phone = $rows[2];
                $apartmentMapResidentUser->resident_user_email = $rows[7];
                $apartmentMapResidentUser->resident_user_first_name = $rows[3];
                $apartmentMapResidentUser->resident_user_gender = $rows[9];
                $apartmentMapResidentUser->resident_user_birthday = $rows[8];
                $apartmentMapResidentUser->resident_user_is_send_email = 1;
                $apartmentMapResidentUser->resident_user_is_send_notify = 1;
                $apartmentMapResidentUser->building_cluster_id = $apartment->building_cluster_id;
                $apartmentMapResidentUser->building_area_id = $apartment->building_area_id;
                $apartmentMapResidentUser->type = $rows[4];
                $apartmentMapResidentUser->type_relationship = $rows[5];
                $apartmentMapResidentUser->ngay_dang_ky_tam_chu = $rows[10];
                $apartmentMapResidentUser->ngay_dang_ky_nhap_khau = $rows[11];
                $apartmentMapResidentUser->cmtnd = $rows[12];
                $apartmentMapResidentUser->ngay_cap_cmtnd = $rows[13];
                $apartmentMapResidentUser->noi_cap_cmtnd = $rows[14];
                if (!$apartmentMapResidentUser->save()) {
                    Yii::error('ApartmentMapResidentUserCreateError' . $rows[1]);
                    Yii::error($apartmentMapResidentUser->getErrors());
                    $message = Yii::t('frontend', "Thêm cư dân vào căn hộ không thành công: ". $rows[1] . '_' .$rows[2]);
                    if($apartmentMapResidentUser->errors){
                        foreach ($apartmentMapResidentUser->errors as $k => $v){
                            $message = $v[0];
                        }
                    }
                    $ApartmentMapResidentUserCreateError[] = [
                        'line' => $i - 2,
                        'apartment_name' => $rows[1],
                        'apartment_parent_path' => $apartment_parent_path,
                        'message' =>  $message
                    ];
                    continue;
                };

                //loại chủ hộ khác
                if($apartmentMapResidentUser->type == ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD){
                    $apartmentMapResidentUsers = ApartmentMapResidentUser::find()
                        ->where(['type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD, 'apartment_id' => $apartmentMapResidentUser->apartment_id])
                        ->andWhere(['<>', 'id', $apartmentMapResidentUser->id])
                        ->all();
                    foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser){
                        $apartmentMapResidentUser->type = ApartmentMapResidentUser::TYPE_MEMBER;
                        if(!$apartmentMapResidentUser->save()){
                            Yii::error($apartmentMapResidentUser->errors);
                        }
                    }
                }
                $apartmentMapResidentUser->syncDataToApartmentMap();
                $apartment->date_received = $rows[6];
                if (!$apartment->save()) {
                    Yii::error('apartment save error ' . $rows[1]);
                    Yii::error($apartment->errors);
                    continue;
                }
            }
            $imported++;
        }
        $success = true;
        $message = Yii::t('frontend', "Import success");
        if ($imported <= 0) {
            $message = Yii::t('frontend', "Import Error");
        }
        return [
            'success' => $success,
            'message' => $message,
            'ApartmentArrayError' => $ApartmentArrayError,
            'ResidentUserCreateError' => $ResidentUserCreateError,
            'ResidentUserExist' => $ResidentUserExist,
            'ApartmentMapResidentUserError' => $ApartmentMapResidentUserError,
            'PhoneErrors' => $PhoneErrors,
            'ApartmentMapResidentUserCreateError' => $ApartmentMapResidentUserCreateError,
            'TotalRow' => $i - 2,
            'TotalImport' => $imported,
        ];
    }

    public function genForm()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $file_path = '/uploads/fee/' . time() . '-resident-user.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách cư dân');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên bất động sản');
        $sheet->setCellValue('C1', 'Số Điện Thoại');
        $sheet->setCellValue('D1', 'Họ Và Tên');
        $sheet->setCellValue('E1', 'Vai Trò');
        $sheet->setCellValue('F1', 'Mối quan hệ');
        $sheet->setCellValue('G1', 'Ngày Nhận Nhà');
        $sheet->setCellValue('H1', 'Email');
        $sheet->setCellValue('I1', 'Ngày Sinh');
        $sheet->setCellValue('J1', 'Giới Tính');
        $sheet->setCellValue('K1', 'Ngày đăng ký tạm trú');
        $sheet->setCellValue('L1', 'Ngày nhập khẩu');
        $sheet->setCellValue('M1', 'Số CMND/CCCD/ Hộ chiếu');
        $sheet->setCellValue('N1', 'Ngày cấp');
        $sheet->setCellValue('O1', 'Nơi cấp');
        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J','K','L','M','N','O'];
        $spreadsheet->getActiveSheet()->getStyle('A1:' . end($arrColumns) . '1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . end($arrColumns) . '1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . end($arrColumns) . '1')->getFill()->getStartColor()->setARGB('10a54a');
        foreach ($arrColumns as $column) {
            $w = 25;
            if ($column == 'A') {
                $w = 15;
            }
            $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth($w);
            $spreadsheet->getActiveSheet()->getStyle($column . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle($column . '1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }
        $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . end($arrColumns) . '1')->getAlignment()->setWrapText(true);
        $type_list = ApartmentMapResidentUser::$type_list;
        $type_list_str = implode(',', $type_list);

        $type_relationship_list = ApartmentMapResidentUser::$type_relationship_list;
        $type_relationship_list_str = implode(',', $type_relationship_list);

        $gender_list_str = implode(',', ['Nam', 'Nữ']);
        for ($i = 2; $i < 5; $i++){
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, 'A01.0'.($i-1));
            $sheet->setCellValue('C' . $i, '0981xxxx0'.$i);
            $sheet->setCellValue('D' . $i, 'Nguyễn Như A');
            $validation = $sheet->getCell('E' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $type_list_str . '"');

            $validation = $sheet->getCell('F' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $type_relationship_list_str . '"');

            $sheet->setCellValue('G' . $i, date('d/m/Y'));
            $sheet->getStyle('G' . $i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');

            $sheet->setCellValue('H' . $i, 'email'.$i.'@gmail.com');

            $sheet->setCellValue('I' . $i, date('d/m/Y'));
            $sheet->getStyle('I' . $i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');

            $validation = $sheet->getCell('J' . $i)->getDataValidation();
            $sheet->setCellValue('J' . $i, 'Nam');
            $sheet->setCellValue('K' . $i, date('d/m/Y'));
            $sheet->getStyle('K' . $i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
            $sheet->setCellValue('L' . $i, date('d/m/Y'));
            $sheet->getStyle('L' . $i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $gender_list_str . '"');
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
        $file_path = '/uploads/fee/' . time() . '-resident-user-list.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách cư dân');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Họ Và Tên');
        $sheet->setCellValue('C1', 'Số Điện Thoại');
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'Giới tính');
        $sheet->setCellValue('F1', 'Ngày sinh');
        $sheet->setCellValue('G1', 'Công việc');
        $sheet->setCellValue('H1', 'Ngày đăng ký tạm trú');
        $sheet->setCellValue('I1', 'Ngày nhập khẩu');
        $sheet->setCellValue('K1', 'Thẻ căn cước');
        $sheet->setCellValue('L1', 'Ngày cấp căn cước');
        $sheet->setCellValue('M1', 'Nơi cấp căn cước');
        $sheet->setCellValue('N1', 'Quốc tịch');
        $sheet->setCellValue('O1', 'Số thị thực');
        $sheet->setCellValue('P1', 'Ngày hết hạn thị thực');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'K', 'L', 'M', 'N', 'O', 'P'];
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
        $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['building_cluster_id' => $buildingCluster->id])->orderBy(['apartment_id' => SORT_ASC])->all();
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser) {
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $apartmentMapResidentUser->resident_user_first_name);
            $phone = preg_replace("/^84/", '0', $apartmentMapResidentUser->resident_user_phone);
            if(strlen($phone) == 10){
                $phone = '84' . substr($phone, 1); 
            }
            $sheet->setCellValue('C' . $i, $phone);
            $sheet->setCellValue('D' . $i, $apartmentMapResidentUser->resident_user_email);
            $sheet->setCellValue('E' . $i, isset(ResidentUser::$gender_list[$apartmentMapResidentUser->resident_user_gender]) ? ResidentUser::$gender_list[$apartmentMapResidentUser->resident_user_gender] : '');

            $resident_user_birthday = $apartmentMapResidentUser->resident_user_birthday;
            $sheet->setCellValue('F'.$i, !empty($resident_user_birthday) ? date('d/m/Y', $resident_user_birthday) : '');
            $sheet->getStyle('F'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');

            $sheet->setCellValue('G' . $i, $apartmentMapResidentUser->resident->work);

            $ngay_dang_ky_tam_chu = $apartmentMapResidentUser->resident->ngay_dang_ky_tam_chu;
            $sheet->setCellValue('H'.$i, !empty($ngay_dang_ky_tam_chu) ? date('d/m/Y', $ngay_dang_ky_tam_chu) : '');
            $sheet->getStyle('H'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');

            $ngay_dang_ky_nhap_khau = $apartmentMapResidentUser->resident->ngay_dang_ky_nhap_khau;
            $sheet->setCellValue('I'.$i, !empty($ngay_dang_ky_nhap_khau) ? date('d/m/Y', $ngay_dang_ky_nhap_khau) : '');
            $sheet->getStyle('I'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');

            $sheet->setCellValue('K' . $i, $apartmentMapResidentUser->resident->cmtnd);
            $ngay_cap_cmtnd = $apartmentMapResidentUser->resident->ngay_cap_cmtnd;
            $sheet->setCellValue('L'.$i, !empty($ngay_cap_cmtnd) ? date('d/m/Y', $ngay_cap_cmtnd) : '');
            $sheet->getStyle('L'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
            $sheet->setCellValue('M' . $i, $apartmentMapResidentUser->resident->noi_cap_cmtnd);
            $sheet->setCellValue('N' . $i, $apartmentMapResidentUser->resident->nationality);

            $sheet->setCellValue('O' . $i, $apartmentMapResidentUser->resident->so_thi_thuc);
            $ngay_het_han_thi_thuc = $apartmentMapResidentUser->resident->ngay_het_han_thi_thuc;
            $sheet->setCellValue('P'.$i, !empty($ngay_het_han_thi_thuc) ? date('d/m/Y', $ngay_het_han_thi_thuc) : '');
            $sheet->getStyle('P'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'success' => true,
            'file_path' => $file_path
        ];
    }
}
