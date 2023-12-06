<?php

namespace frontend\models;

use common\models\ApartmentMapResidentUser;
use common\models\BuildingArea;
use common\models\ServiceBill;
use common\models\ServiceParkingFee;
use common\models\ServiceUtilityBooking;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use common\helpers\CUtils;
use common\models\Apartment;
use common\models\ServiceMapManagement;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServicePaymentFee;

/**
 * ServicePaymentFeeSearch represents the model behind the search form of `common\models\ServicePaymentFee`.
 */
class ServicePaymentFeeSearch extends ServicePaymentFee
{
    public $from_month;
    public $to_month;

    public $month;
    public $year;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
//            [['service_map_management_id'], 'required'],
            [['building_area_id', 'id', 'is_draft', 'service_map_management_id', 'building_cluster_id', 'apartment_id', 'price', 'status', 'fee_of_month', 'day_expired', 'created_at', 'updated_at', 'created_by', 'updated_by', 'from_month', 'to_month', 'month', 'year'], 'integer'],
            [['description'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $is_export = false)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $query = ServicePaymentFeeResponse::find()->where(['building_cluster_id' => $buildingCluster->id]);
        $queryCount = ServicePaymentFeeResponse::find()->where(['building_cluster_id' => $buildingCluster->id]);

        // add conditions that should always apply here
        $arrayActive = [
            'query' => $query,
            'pagination' => [
                'pageSize' => 1000,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC,
                ]
            ],
        ];

        if($is_export == false){
            $arrayActive['pagination'] = [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ];
        }

        $dataProvider = new ActiveDataProvider($arrayActive);

        $this->load(CUtils::modifyParams($params), '');

        if (!$this->validate()) {
            Yii::error($this->getErrors());
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return [
                'dataCount' => 0,
                'dataProvider' => $dataProvider
            ];
        }

        $floorIds = [];
        if(!empty($this->building_area_id)){
            $floorIds[] = $this->building_area_id;
            $floors = BuildingArea::find()->where(['parent_id' => $this->building_area_id, 'is_deleted' => BuildingArea::NOT_DELETED])->all();
            foreach ($floors as $floor){
                $floorIds[] = $floor->id;
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'service_map_management_id' => $this->service_map_management_id,
            'apartment_id' => $this->apartment_id,
            'price' => $this->price,
            'is_draft' => $this->is_draft,
            'status' => $this->status,
            'fee_of_month' => $this->fee_of_month,
            'day_expired' => $this->day_expired,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'building_area_id' => $floorIds,
        ]);

        $queryCount->andFilterWhere([
            'id' => $this->id,
            'service_map_management_id' => $this->service_map_management_id,
            'apartment_id' => $this->apartment_id,
            'price' => $this->price,
            'is_draft' => $this->is_draft,
            'status' => $this->status,
            'fee_of_month' => $this->fee_of_month,
            'day_expired' => $this->day_expired,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'building_area_id' => $floorIds,
        ]);

        $start_time = null;
        $end_time = null;
        if(!empty($this->month) && !empty($this->year)){
            $start_time = strtotime(date($this->year.'-'.$this->month.'-01 00:00:00'));
//            $end_time = strtotime(date($this->year.'-'.$this->month.'-t 23:59:59'));
            $end_time = strtotime('+1 month', strtotime(date('Y-m-01', $start_time)));
        }else if(empty($this->month) && !empty($this->year)){
            $start_time = strtotime(date($this->year.'-01-01 00:00:00'));
//            $end_time = strtotime(date($this->year.'-12-t 23:59:59'));
            $end_time = strtotime('+1 month', strtotime(date($this->year.'-12-01')));
        }else if(!empty($this->month) && empty($this->year)){
            if($this->month < 10){
                $this->month = '0'.(int)$this->month;
            }
            $query->andWhere(['like', "FROM_UNIXTIME(fee_of_month, '%d/%m/%Y')", "/".$this->month."/"]);
            $queryCount->andWhere(['like', "FROM_UNIXTIME(fee_of_month, '%d/%m/%Y')", "/".$this->month."/"]);
        }
        if(!empty($start_time) && !empty($end_time)){
            $query->andWhere(['and', "fee_of_month >= $start_time", "fee_of_month < $end_time"]);
            $queryCount->andWhere(['and', "fee_of_month >= $start_time", "fee_of_month < $end_time"]);
        }
        $query->andFilterWhere(['like', 'description', $this->description]);
        $queryCount->andFilterWhere(['like', 'description', $this->description]);
        $dataCount = $queryCount->select(["SUM(price) as price","SUM(money_collected) as money_collected","SUM(more_money_collecte) as more_money_collecte"])->one();

        if($is_export == true){
            return self::exportPaymentFee($dataCount, $dataProvider);
        }

        return [
            'dataCount' => $dataCount,
            'dataProvider' => $dataProvider
        ];
    }

    public function searchDebt($params)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $query = Apartment::find()->where(['building_cluster_id' => $buildingCluster->id, 'is_deleted' => Apartment::NOT_DELETED]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 1000,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load(CUtils::modifyParams($params), '');

        if (!empty($this->building_area_id)) {
            $buildingAreas = BuildingArea::find()->where(['parent_id' => $this->building_area_id])->all();
            $building_area_ids = [];
            foreach ($buildingAreas as $buildingArea){
                $building_area_ids[] = $buildingArea->id;
            }
            $query->andFilterWhere(['building_area_id' => $building_area_ids]);
        }

        $res = [];
//        $countDebtApartments = $query->all();
        $countDebtApartments = $dataProvider->getModels();
        foreach ($countDebtApartments as $countDebtApartment){
            $sum = ServicePaymentFee::find()->select('sum(price) as total_debt')->where(['apartment_id' => $countDebtApartment->id, 'status' => ServicePaymentFee::STATUS_UNPAID, 'is_draft' => ServicePaymentFee::IS_NOT_DRAFT])->groupBy(['apartment_id'])->one();
            $res[] = [
                'apartment_id' => $countDebtApartment->id,
                'apartment_name' => $countDebtApartment->name,
                'apartment_parent_path' => trim($countDebtApartment->parent_path, '/'),
                'apartment_building_area_id' => $countDebtApartment->building_area_id,
                'total_debt' => !empty($sum) ? (int)$sum->total_debt : 0,
            ];
        }
        return $res;
    }

    private function exportPaymentFee($dataCount, $dataProvider)
    {
        $file_path = '/uploads/fee/' . 'Danh sách phí.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách phí');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tháng');
        $sheet->setCellValue('C1', 'Năm');
        $sheet->setCellValue('D1', 'Căn hộ');
        $sheet->setCellValue('E1', 'Lô');
        $sheet->setCellValue('F1', 'Tầng');
        $sheet->setCellValue('G1', 'Khách hàng');
        $sheet->setCellValue('H1', 'Loại dịch vụ');
        $sheet->setCellValue('I1', 'Chi tiết');
        $sheet->setCellValue('J1', 'Phải thu (vnđ)');
        $sheet->setCellValue('K1', 'Đã thu (vnđ)');
        $sheet->setCellValue('L1', 'Còn phải thu (vnđ)');
        $sheet->setCellValue('M1', 'Ngày tạo');
        $sheet->setCellValue('N1', 'Người duyệt');
        $sheet->setCellValue('O1', 'Phiếu thu');
        $sheet->setCellValue('P1', 'Tình trạng');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getFill()->getStartColor()->setARGB('10a54a');
        foreach ($arrColumns as $column){
            $w = 25;
            if($column == 'A'){
                $w = 10;
            }else if($column == 'I'){
                $w = 60;
            }
            $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth($w);
            $spreadsheet->getActiveSheet()->getStyle($column.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle($column.'1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }
        $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].'1:'.end($arrColumns).'1')->getAlignment()->setWrapText(true);

        $i = 2;
        foreach ($dataProvider->getModels() as $item){
            if(-1 == $item->status)
            {
                continue;
            }
            $lo = '';
            $tang = '';
            if($item->apartment){
                list($lo, $tang) = explode('/', $item->apartment->parent_path);
            }
            $sheet->setCellValue('A'.$i, $i-1);
            $sheet->setCellValue('B'.$i, (string)date('m', $item->fee_of_month));
            $sheet->setCellValue('C'.$i, (string)date('Y', $item->fee_of_month));
            $sheet->setCellValue('D'.$i, ($item->apartment)?$item->apartment->name:'');
            $sheet->setCellValue('E'.$i, $lo);
            $sheet->setCellValue('F'.$i, $tang);
            $sheet->setCellValue('G'.$i, ($item->apartment)?$item->apartment->resident_user_name:'');

            $service_name = '';
            if($item->for_type == ServicePaymentFee::FOR_TYPE_1){
                $service_name = 'Đặt cọc - ';
            }else if($item->for_type == ServicePaymentFee::FOR_TYPE_2){
                $service_name = 'Phát sinh - ';
            }
            if (!empty($item->serviceMapManagement)) {
                $service_name .= $item->serviceMapManagement->service_name;
                if($item->type == ServicePaymentFee::TYPE_SERVICE_PARKING_FEE){
                    $serviceParkingFee = ServiceParkingFee::findOne(['service_payment_fee_id' => $item->id]);
                    if(!empty($serviceParkingFee)){
                        if(!empty($serviceParkingFee->serviceManagementVehicle)){
                            $service_name .= ' - BKS: ' . $serviceParkingFee->serviceManagementVehicle->number;
                        }
                    }
                }else{
                    //nếu phí từ book sẽ lấy thêm thông tin tiện ích
                    $booking = ServiceUtilityBooking::find()->where(['like', 'service_payment_fee_ids_text_search', ','.$item->id.','])->one();
                    if(!empty($booking)){
                        if(!empty($booking->serviceUtilityFree)){
                            $service_name .= ' - ' .$booking->serviceUtilityFree->name;
                        }
                    }
                }
            }
            $sheet->setCellValue('H'.$i, $service_name);
            $sheet->setCellValue('I'.$i, $item->description);
            $sheet->setCellValue('J'.$i, $item->price);
            $sheet->getStyle('J'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('K'.$i, $item->money_collected);
            $sheet->getStyle('K'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('L'.$i, $item->more_money_collecte);
            $sheet->getStyle('L'.$i)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue('M'.$i, date('d/m/Y', $item->created_at));
            $sheet->getStyle('M'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');

            $approved_by_name = '';
            if(!empty($item->managementUser)){
                $approved_by_name = $item->managementUser->first_name;
            }
            $sheet->setCellValue('N'.$i, $approved_by_name);
            $bill_code = '';
            if(!empty($item->service_bill_ids)){
                $ids = json_decode($item->service_bill_ids, true);
                $ServiceBills = ServiceBill::find()->where(['id' => $ids])->all();
                foreach ($ServiceBills as $serviceBill){
                    $bill_code .= $serviceBill->number."\r\n ";
                }
            }
            $sheet->setCellValue('O'.$i, $bill_code);
            $sheet->setCellValue('P'.$i, (ServicePaymentFee::$statusList[$item->status])?ServicePaymentFee::$statusList[$item->status]:'');

            $i++;
        }
        $spreadsheet->getActiveSheet()->getStyle('I1:I'.$i)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('I1:I'.$i)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('I1:I'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $spreadsheet->getActiveSheet()->mergeCells($arrColumns[0].$i.':I'.$i);
        $sheet->setCellValue('A'.$i, 'Tổng (vnđ)');
        $sheet->setCellValue('J'.$i, (int)$dataCount->price);
        $sheet->getStyle('J'.$i)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->setCellValue('K'.$i, (int)$dataCount->money_collected);
        $sheet->getStyle('K'.$i)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->setCellValue('L'.$i, (int)$dataCount->more_money_collecte);
        $sheet->getStyle('L'.$i)->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFill()->getStartColor()->setARGB('e8e8e8');
        $spreadsheet->getActiveSheet()->getRowDimension($i)->setRowHeight(25);

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function getAllTotalRevenue($params, $is_export = false)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $query           = ServicePaymentFeeResponse::find()->where(['building_cluster_id' => $buildingCluster->id, 'is_draft' => 0]);       

        // add conditions that should always apply here
        $arrayActive = [
            'query' => $query,
            'pagination' => [
                'pageSize' => 10000,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC,
                ]
            ],
        ];

        if($is_export == false){
            $arrayActive['pagination'] = [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 10000,
            ];
        }

        $dataProvider = new ActiveDataProvider($arrayActive);

//         $this->load(CUtils::modifyParams($params), '');

//         if (!$this->validate()) {
//             Yii::error($this->getErrors());
//             // uncomment the following line if you do not want to return any records when validation fails
//             $query->where('0=1');
//             return [
//                 'dataCount' => 0,
//                 'dataProvider' => $dataProvider
//             ];
//         }

//         $floorIds = [];
//         if(!empty($this->building_area_id)){
//             $floorIds[] = $this->building_area_id;
//             $floors = BuildingArea::find()->where(['parent_id' => $this->building_area_id, 'is_deleted' => BuildingArea::NOT_DELETED])->all();
//             foreach ($floors as $floor){
//                 $floorIds[] = $floor->id;
//             }
//         }

//         // grid filtering conditions
//         $query->andFilterWhere([
//             'id' => $this->id,
//             'service_map_management_id' => $this->service_map_management_id,
//             'apartment_id' => $this->apartment_id,
//             'price' => $this->price,
//             'is_draft' => $this->is_draft,
//             'status' => $this->status,
//             'fee_of_month' => $this->fee_of_month,
//             'day_expired' => $this->day_expired,
//             'created_at' => $this->created_at,
//             'updated_at' => $this->updated_at,
//             'created_by' => $this->created_by,
//             'updated_by' => $this->updated_by,
//             'building_area_id' => $floorIds,
//         ]);
        $start_time = null;
        $end_time = null;
        $month = Yii::$app->request->get('month', null);
        $year  = Yii::$app->request->get('year', null);
        if(!empty($month) && !empty($year)){
            $start_time = strtotime(date($year.'-'.$month.'-01 00:00:00'));
//            $end_time = strtotime(date($year.'-'.$month.'-t 23:59:59'));
            $end_time = strtotime('+1 month', strtotime(date('Y-m-01', $start_time)));
        }else if(empty($month) && !empty($year)){
            $start_time = strtotime(date($year.'-01-01 00:00:00'));
//            $end_time = strtotime(date($year.'-12-t 23:59:59'));
            $end_time = strtotime('+1 month', strtotime(date($year.'-12-01')));
        }else if(!empty($month) && empty($year)){
            if($month < 10){
                $month = '0'.(int)$month;
            }
            $query->andWhere(['like', "FROM_UNIXTIME(fee_of_month, '%d/%m/%Y')", "/".$month."/"]);
        }
        if(!empty($month) && !empty($year)){
            $query->andWhere(['and', "fee_of_month >= $start_time", "fee_of_month < $end_time"]);
        }
//         $query->andFilterWhere(['like', 'description', $this->description]);
        return $dataProvider;
    }
}
