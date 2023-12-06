<?php

namespace frontend\models;

use common\helpers\CUtils;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceBill;

/**
 * ServiceBillSearch represents the model behind the search form of `common\models\ServiceBill`.
 */
class ServiceBillSearch extends ServiceBill
{
    public $start_time;
    public $end_time;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_time', 'end_time', 'id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'management_user_id', 'resident_user_id', 'type_payment', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by', 'type'], 'integer'],
            [['number', 'management_user_name', 'bank_name', 'bank_account', 'bank_holders'], 'string'],
            [['code', 'resident_user_name', 'payer_name', 'status'], 'safe'],
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
        $query = ServiceBillResponse::find()->with(['apartment', 'serviceProvider', 'managementUser'])->where(['is_deleted' => ServiceBill::NOT_DELETED, 'building_cluster_id' => $buildingCluster->id]);
        $queryCount = ServiceBillResponse::find()->with(['apartment', 'serviceProvider', 'managementUser'])->where(['is_deleted' => ServiceBill::NOT_DELETED, 'building_cluster_id' => $buildingCluster->id]);

        // add conditions that should always apply here
        $arrayActive = [
            'query' => $query,
            'pagination' => [
                'pageSize' => 10000,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ];
        if($is_export == false){
            $arrayActive['pagination'] = [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ];
        }
        $dataProvider = new ActiveDataProvider($arrayActive);

        $status_arr = [];
        if(!empty($params['status'])){
            $status_arr = explode(',', $params['status']);
            unset($params['status']);
        }else{
            $query->andWhere(['not in','status',[ServiceBill::STATUS_DRAFT]]);
            $queryCount->andWhere(['not in','status',[ServiceBill::STATUS_DRAFT]]);
        }
        $this->load(CUtils::modifyParams($params),'');
        if(!empty($status_arr)){
            $this->status = $status_arr;
        }
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            $dataCountRes[] = [
                'type_payment' => 0,
                'total_price' => 0,
            ];
            return [
                'dataCount' => $dataCountRes,
                'dataProvider' => $dataProvider
            ];
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'building_cluster_id' => $this->building_cluster_id,
            'building_area_id' => $this->building_area_id,
            'apartment_id' => $this->apartment_id,
            'management_user_id' => $this->management_user_id,
            'resident_user_id' => $this->resident_user_id,
            'type_payment' => $this->type_payment,
            'status' => $this->status,
            'type' => $this->type,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $queryCount->andFilterWhere([
            'id' => $this->id,
            'building_cluster_id' => $this->building_cluster_id,
            'building_area_id' => $this->building_area_id,
            'apartment_id' => $this->apartment_id,
            'management_user_id' => $this->management_user_id,
            'resident_user_id' => $this->resident_user_id,
            'type_payment' => $this->type_payment,
            'status' => $this->status,
            'type' => $this->type,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'management_user_name', $this->management_user_name])
            ->andFilterWhere(['like', 'resident_user_name', $this->resident_user_name])
            ->andFilterWhere(['like', 'payer_name', $this->payer_name]);

        $queryCount->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'management_user_name', $this->management_user_name])
            ->andFilterWhere(['like', 'resident_user_name', $this->resident_user_name])
            ->andFilterWhere(['like', 'payer_name', $this->payer_name]);

        if(!empty($this->start_time) && !empty($this->end_time)){
            $query->andWhere(['and', "payment_date >= $this->start_time", "payment_date <= $this->end_time"]);
            $queryCount->andWhere(['and', "payment_date >= $this->start_time", "payment_date <= $this->end_time"]);
        }

        $dataCounts = $queryCount->select(["SUM(total_price) as total_price", "type_payment"])->groupBy(['type_payment'])->all();
        $dataCountRes = [];
        foreach ($dataCounts as $dataCount){
            $dataCountRes[] = [
                'type_payment' => $dataCount->type_payment,
                'total_price' => $dataCount->total_price,
            ];
        }

        if($is_export == true){
            return self::exportPaymentFee($dataCountRes, $dataProvider, $this->type, $status_arr);
        }

        return [
            'dataCount' => $dataCountRes,
            'dataProvider' => $dataProvider
        ];
    }

    private function exportPaymentFee($dataCountRes, $dataProvider, $type = ServiceBill::TYPE_0, $status_arr = [])
    {
        $name_file = 'phiếu thu';
        $j1 = 'Người nộp';
        $k1 = 'Người thu';
        if($type == ServiceBill::TYPE_1){
            $k1 = 'Người chi';
            $j1 = 'Người nhận';
            $name_file = 'phiếu chi';
        }
        if(in_array(ServiceBill::STATUS_CANCEL, $status_arr)){
            $name_file .= ' huỷ';
        }
        if(in_array(ServiceBill::STATUS_BLOCK, $status_arr)){
            $name_file = 'sổ quỹ';
        }
        $file_path = '/uploads/fee/Danh sách '.$name_file.'.xlsx';
        
        if(in_array(ServiceBill::STATUS_BLOCK, $status_arr)){

            $file_path = '/uploads/fee/Danh sách sổ quỹ.xlsx';
        }
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách '.$name_file);
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Ngày chứng từ');
        $sheet->setCellValue('C1', 'Số chứng từ');
        $sheet->setCellValue('D1', 'Hình thức thanh toán');
        $sheet->setCellValue('E1', 'Căn hộ');
        $sheet->setCellValue('F1', 'Lô');
        $sheet->setCellValue('G1', 'Tầng');
        $sheet->setCellValue('H1', 'Tên khách hàng');
        $sheet->setCellValue('I1', 'Số tiền (vnđ)');
        $sheet->setCellValue('J1', $j1);
        $sheet->setCellValue('K1', $k1);
        $sheet->setCellValue('L1', 'Ngày thực hiện');
        $sheet->setCellValue('M1', 'Tình trạng');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
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
        foreach ($dataProvider->getModels() as $item){
            $lo = '';
            $tang = '';
            if($item->apartment){
                list($lo, $tang) = explode('/', $item->apartment->parent_path);
            }
            $sheet->setCellValue('A'.$i, $i-1);
            $sheet->setCellValue('B'.$i, date('d/m/Y', $item->execution_date));
            $sheet->getStyle('B'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');

            $sheet->setCellValue('C'.$i, $item->number);
            $sheet->setCellValue('D'.$i, ServiceBill::$type_payment_lst[$item->type_payment]);

            $sheet->setCellValue('E'.$i, ($item->apartment)?$item->apartment->name:'');
            $sheet->setCellValue('F'.$i, $lo);
            $sheet->setCellValue('G'.$i, $tang);
            $sheet->setCellValue('H'.$i, ($item->apartment)?$item->apartment->resident_user_name:'');
            $sheet->setCellValue('I'.$i, $item->total_price);
            $sheet->getStyle('I'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('J'.$i, $item->payer_name);
            $sheet->setCellValue('K'.$i, $item->management_user_name);
            $sheet->setCellValue('L'.$i, date('d/m/Y', $item->payment_date));
            $sheet->getStyle('L'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
            $sheet->setCellValue('M'.$i, (ServiceBill::$status_lst[$item->status])?ServiceBill::$status_lst[$item->status]:'');
            $i++;
        }

        if(in_array(ServiceBill::STATUS_BLOCK, $status_arr)){
            $sheet->setCellValue('I'.$i, 'Tổng (vnđ)');
            $sheet->setCellValue('J'.$i, 'Chuyển khoản (vnđ)');
            $sheet->setCellValue('K'.$i, 'Tiền mặt (vnđ)');
            $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFill()->setFillType(Fill::FILL_SOLID);
            $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].$i.':'.end($arrColumns).$i)->getFill()->getStartColor()->setARGB('e8e8e8');
            $spreadsheet->getActiveSheet()->getRowDimension($i)->setRowHeight(25);
            $tien_mat = 0;
            $chuyen_khoan = 0;
            foreach ($dataCountRes as $item){
                if($item['type_payment'] == 0){
                    $tien_mat += $item['total_price'];
                }else{
                    $chuyen_khoan += $item['total_price'];
                }
            }
            $sheet->setCellValue('I'.($i+1), ($chuyen_khoan + $tien_mat));
            $sheet->getStyle('I'.($i+1))->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('J'.($i+1), $chuyen_khoan);
            $sheet->getStyle('J'.($i+1))->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('K'.($i+1), $tien_mat);
            $sheet->getStyle('K'.($i+1))->getNumberFormat()->setFormatCode('#,##0');
            $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].($i+1).':'.end($arrColumns).($i+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].($i+1).':'.end($arrColumns).($i+1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle($arrColumns[0].($i+1).':'.end($arrColumns).($i+1))->getFont()->setBold(true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path
        ];
    }
}
