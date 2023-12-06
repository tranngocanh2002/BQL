<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceMapManagement;
use common\models\ServicePaymentFee;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServicePaymentFeeImportForm")
 * )
 */
class ServicePaymentFeeImportForm extends Model
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
            [['service_map_management_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceMapManagement::className(), 'targetAttribute' => ['service_map_management_id' => 'id']],
        ];
    }

    public function import()
    {
        $user = Yii::$app->user->getIdentity();
        $building_cluster_id = $user->building_cluster_id;
        $service_map_management_id = $this->service_map_management_id;
        $is_validate = $this->is_validate;

        $fileandpath = \Yii::getAlias('@webroot') . $this->file_path;
        $spreadsheet = IOFactory::load($fileandpath);
//        $xls_datas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $xls_datas = $spreadsheet->getActiveSheet();

        $serviceMapManagement = ServiceMapManagement::findOne(['id' => $service_map_management_id, 'building_cluster_id' => $building_cluster_id]);
        if (empty($serviceMapManagement)) {
            return false;
        }
        $imported = 0;
        $apartmentMapResidentUserArrayError = [];
        $arrCreateError = [];
        $i = 2;
        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        while (true) {
            $rows = [];
            $stop = 0;
            foreach ($arrColumns as $col) {
                $cell = $xls_datas->getCell($col . $i);
                $val = $cell->getFormattedValue();
                $val = trim($val);
                if ($col == 'A' && empty($val)) {
                    $stop = 1;
                    break;
                }
                if ($col == 'C' && !empty($val)) {
                    $val = $val .'/';
                }
                if($col == 'F' || $col == 'G'){
                    // if(empty($val)){
                    //     $val = '';
                    // }
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
                }
                $rows[] = $val;

            }
            if ($stop == 1) {
                break;
            }
            $i++;
            if(count($arrColumns) > count($rows)){
                continue;
            }


            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['building_cluster_id' => $building_cluster_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD,'apartment_name' => $rows[1], 'apartment_parent_path' => $rows[2]]);
            if (empty($apartmentMapResidentUser)) {
                $apartmentMapResidentUserArrayError[] = [
                    'line' => $i - 2,
                    'apartment_name' => $rows[1],
                    'apartment_parent_path' => $rows[2],
                    'message' => Yii::t('frontend', "apartment map resident empty: " .$rows[1])
                ];
            }else{
                if ($is_validate == 0) {
                    $servicePaymentFee = new ServicePaymentFee();
                    $servicePaymentFee->service_map_management_id = $service_map_management_id;
                    $servicePaymentFee->building_cluster_id = $building_cluster_id;
                    $servicePaymentFee->building_area_id = $apartmentMapResidentUser->building_area_id;
                    $servicePaymentFee->apartment_id = $apartmentMapResidentUser->apartment_id;
                    $servicePaymentFee->description = $rows[3];
                    $servicePaymentFee->status = ServicePaymentFee::STATUS_UNPAID;
                    $servicePaymentFee->price = $rows[4];
                    $servicePaymentFee->fee_of_month = $rows[5];
                    $servicePaymentFee->day_expired = $rows[6];
                    if (!$servicePaymentFee->save()) {
                        $arrCreateError[] = [
                            'line' => $i - 2,
                            'apartment_name' => $rows[1],
                            'apartment_parent_path' => $rows[2],
                            'errors' => $servicePaymentFee->errors,
                            'message' => Yii::t('frontend', "create service payment fee error")
                        ];
                    };
                }
                $imported++;
            }
        }
        return [
            'success' => true,
            'message' => Yii::t('frontend', "Import success"),
            'apartmentMapResidentUserArrayError' => $apartmentMapResidentUserArrayError,
            'arrCreateError' => $arrCreateError,
            'TotalRow' => $i - 2,
            'TotalImport' => $imported,
        ];
    }

    public function genForm()
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $file_path = '/uploads/fee/'.time().'-service-payment-fee.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên căn hộ');
        $sheet->setCellValue('C1', 'Phân Khu');
        $sheet->setCellValue('D1', 'Mô tả');
        $sheet->setCellValue('E1', 'Số tiền');
        $sheet->setCellValue('F1', 'Phí của tháng');
        $sheet->setCellValue('G1', 'Thời hạn nộp phí');
        $apartmentMapResidentUsers = ApartmentMapResidentUser::find()->where(['building_cluster_id' => $building_cluster_id, 'type' => ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD])->orderBy(['apartment_id' => SORT_ASC])->all();
        $i = 2;
        $ids = [];
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser){
            if(isset($ids[$apartmentMapResidentUser->apartment_id])){ continue;}
            $ids[$apartmentMapResidentUser->apartment_id] = $apartmentMapResidentUser->apartment_id;
            $sheet->setCellValue('A'.$i, $i-1);
            $sheet->setCellValue('B'.$i, $apartmentMapResidentUser->apartment_name);
            $sheet->setCellValue('C'.$i, trim($apartmentMapResidentUser->apartment_parent_path, '/'));
//            $sheet->setCellValue('F'.$i, date('m/Y'));
            $sheet->setCellValue('F'.$i, date('d/m/Y'));
            $sheet->getStyle('F'.$i)
                ->getNumberFormat()
                ->setFormatCode('mm/yyyy');
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path
        ];
    }
}
