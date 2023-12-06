<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use frontend\models\AnnouncementItemSearch;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;
use common\models\ApartmentMapResidentUser;
use common\models\AnnouncementItem;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ServiceAnnouncementCampaignExportForm")
 * )
 */
class ServiceAnnouncementCampaignExportForm extends Model
{
    /**
     * @SWG\Property(description="is validate: 0 - là ko validate, 1 - validate", default="", type="integer")
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

    public function export($params, $is_export = false)
    {
        $announcementCampaignType = $params['announcement_campaign_type'] ?? 0;
        switch($announcementCampaignType)
        {
            case 0: return $this->genfromRegularNotifications(); 
                    break;            
            case 1: return $this->genfromFeeNotifications(); 
                    break;
            case 2: return $this->genfromSurveyNotifications();
                    break;
        }
        
    }
    public function genfromRegularNotifications()
    {
        $AnnouncementItemSearch = new AnnouncementItemSearch();
        $data = $AnnouncementItemSearch->searchByIdAnnouncementCampaign(Yii::$app->request->queryParams);
        $dataCount      = $data['dataCount'];
        $dataProvider   = $data['dataProvider'];
        $dataResult     = $dataProvider->getModels();

        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $file_path = '/uploads/fee/'.'Danh sách gửi thông báo thường.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách thông báo thường');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên bất động sản');
        $sheet->setCellValue('C1', 'Họ và Tên');
        $sheet->setCellValue('D1', 'Địa chỉ');
        $sheet->setCellValue('E1', 'Email');
        $sheet->setCellValue('F1', 'App');

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

        $apartmentMapResidentUsers  = $dataResult;
        $i = 2;
        $ids = [];
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser){
            $apartment= Apartment::findOne(['id'=> $apartmentMapResidentUser['apartment_id']]); 
            $sheet->setCellValue('A'.$i, $i-1);
            $sheet->setCellValue('B'.$i, $apartment->name ?? "");
            $sheet->setCellValue('C'.$i, $apartmentMapResidentUser['resident_user_name'] ?? "");
            $sheet->setCellValue('D'.$i, $apartment->parent_path ?? "");
            $sheet->setCellValue('E'.$i, $apartmentMapResidentUser['email'] ?? "");
            $sheet->setCellValue('F'.$i, $apartmentMapResidentUser['type'] ? "Đã xem" : "Đã gửi");
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path,
            // 'apartmentMapResidentUsers' => $apartmentMapResidentUsers
        ];
    }
    public function genfromSurveyNotifications()
    {
        $AnnouncementItemSearch = new AnnouncementItemSearch();
        $data = $AnnouncementItemSearch->searchByIdAnnouncementCampaign(Yii::$app->request->queryParams);
        $dataCount      = $data['dataCount'];
        $dataProvider   = $data['dataProvider'];
        $dataResult     = $dataProvider->getModels();

        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $file_path = '/uploads/fee/'.'Danh sách gửi thông báo khảo sát.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách thông báo khảo sát');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên bất động sản');
        $sheet->setCellValue('C1', 'Họ và Tên');
        $sheet->setCellValue('D1', 'Địa chỉ');
        $sheet->setCellValue('E1', 'Email');
        $sheet->setCellValue('F1', 'App');
        $sheet->setCellValue('G1', 'Kết quả');
        $sheet->setCellValue('H1', 'Thời gian');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F','G','H'];
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
        $spreadsheet->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setWrapText(true);

        $apartmentMapResidentUsers  = $dataResult;
        $i = 2;
        $ids = [];
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser){
            $apartment = Apartment::findOne(['id'=> $apartmentMapResidentUser['apartment_id']]);
            $sheet->setCellValue('A'.$i, $i-1);
            $sheet->setCellValue('B'.$i, $apartment->name ?? "");
            $sheet->setCellValue('C'.$i, $apartmentMapResidentUser['resident_user_name'] ?? "");
            $sheet->setCellValue('D'.$i, $apartment->parent_path ?? "");
            $sheet->setCellValue('E'.$i, $apartmentMapResidentUser['email'] ?? "");
            $sheet->setCellValue('F'.$i, $apartmentMapResidentUser['type'] ? "Đã xem" : "Đã gửi");
            $sheet->setCellValue('G'.$i, $apartmentMapResidentUser['status_notify'] ? "Không đồng ý" : "Đồng ý");
            $sheet->setCellValue('H'.$i, date('Y/m/d H:i:s'));
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path,
            // 'apartmentMapResidentUsers' => $apartmentMapResidentUsers
        ];
    }
    public function genfromFeeNotifications()
    {
        $AnnouncementItemSearch = new AnnouncementItemSearch();
        $data = $AnnouncementItemSearch->searchByIdAnnouncementCampaign(Yii::$app->request->queryParams);
        $dataCount      = $data['dataCount'];
        $dataProvider   = $data['dataProvider'];
        $dataResult     = $dataProvider->getModels();
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $building_cluster_id = $buildingCluster->id;
        $file_path = '/uploads/fee/'.'Danh sách gửi thông báo phí.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách thông báo phí');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên bất động sản');
        $sheet->setCellValue('C1', 'Họ và Tên');
        $sheet->setCellValue('D1', 'Địa chỉ');
        $sheet->setCellValue('E1', 'Nợ cuối kỳ');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'App');
        // $sheet->setCellValue('H1', 'SMS');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F','G'];
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFill()->getStartColor()->setARGB('10a54a');
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
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setWrapText(true);

        $apartmentMapResidentUsers  = $dataResult;
        $i = 2;
        $ids = [];
        foreach ($apartmentMapResidentUsers as $apartmentMapResidentUser){
            $apartment = Apartment::findOne(['id'=> $apartmentMapResidentUser['apartment_id']]);
            $apartmentMapResidentUserApp = "Đã gửi";
            if(!empty($apartmentMapResidentUser->app))
            {
                $apartmentMapResidentUserApp = $apartmentMapResidentUser['app'] ? "Đã xem" : "Đã gửi" ;
            }
            $sheet->setCellValue('A'.$i, $i-1);
            $sheet->setCellValue('B'.$i, $apartment->name ?? "");
            $sheet->setCellValue('C'.$i, $apartmentMapResidentUser['resident_user_name'] ?? "");
            $sheet->setCellValue('D'.$i, $apartment->parent_path ?? "");
            $sheet->setCellValue('E'.$i, $apartmentMapResidentUser->end_debt ?? 0);
            $sheet->setCellValue('F'.$i, $apartmentMapResidentUser['email'] ?? "");
            $sheet->setCellValue('G'.$i, $apartmentMapResidentUserApp);
            // $sheet->setCellValue('H'.$i, $apartmentMapResidentUser['phone'] ?? "");
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path,
            // 'apartmentMapResidentUsers' => $apartmentMapResidentUsers
        ];
    }
}
