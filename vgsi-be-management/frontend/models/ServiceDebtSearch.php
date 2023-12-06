<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\models\Apartment;
use common\models\BuildingArea;
use common\models\ResidentUser;
use frontend\models\AnnouncementSendNewResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceDebt;

/**
 * ServiceDebtSearch represents the model behind the search form of `common\models\ServiceDebt`.
 */
class ServiceDebtSearch extends ServiceDebt
{
    public $type;
    public $building_area_ids;
    public $targets; 
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'building_cluster_id', 'building_area_id', 'apartment_id', 'early_debt', 'end_debt', 'receivables', 'collected', 'month', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'type'], 'integer'],
            [['building_area_ids', 'targets'], 'safe']
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
        $query = ServiceDebtResponse::find()->where(['building_cluster_id' => $buildingCluster->id]);
        $queryCount = ServiceDebtResponse::find()->where(['building_cluster_id' => $buildingCluster->id]);

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
        $this->load(CUtils::modifyParams($params),'');

        $dataProvider = new ActiveDataProvider($arrayActive);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            $dataCountRes = [
                'early_debt' => 0,
                'end_debt' => 0,
                'receivables' => 0,
                'collected' => 0,
            ];
            return [
                'dataCount' => $dataCountRes,
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
            'building_area_id' => $floorIds,
            'apartment_id' => $this->apartment_id,
            'early_debt' => $this->early_debt,
            'end_debt' => $this->end_debt,
            'receivables' => $this->receivables,
            'collected' => $this->collected,
            'month' => $this->month,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $queryCount->andFilterWhere([
            'id' => $this->id,
            'building_area_id' => $floorIds,
            'apartment_id' => $this->apartment_id,
            'early_debt' => $this->early_debt,
            'end_debt' => $this->end_debt,
            'receivables' => $this->receivables,
            'collected' => $this->collected,
            'month' => $this->month,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $dataCount = $queryCount->select(["SUM(early_debt) as early_debt","SUM(end_debt) as end_debt","SUM(receivables) as receivables","SUM(collected) as collected"])->one();
        $dataCountRes = [];
        if(!empty($dataCount)){
            $dataCountRes = [
                'early_debt' => (int)$dataCount->early_debt,
                'end_debt' => (int)$dataCount->end_debt,
                'receivables' => (int)$dataCount->receivables,
                'collected' => (int)$dataCount->collected,
            ];
        }

        if($is_export == true){
            return self::exportPaymentFee($dataCountRes, $dataProvider);
        }

        return [
            'dataCount' => $dataCountRes,
            'dataProvider' => $dataProvider
        ];
    }

    public function searchReminder($params)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        // $query = ServiceDebtReminderResponse::find()->where(['building_cluster_id' => $buildingCluster->id, 'type' => ServiceDebt::TYPE_CURRENT_MONTH]);
        $query = AnnouncementSendNewResponse::find()->where(['building_cluster_id' => $buildingCluster->id, 'is_deleted' => Apartment::NOT_DELETED]); //lấy all danh sách của căn hộ

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load(CUtils::modifyParams($params),'');


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return [
                'dataCount' => 0,
                'dataProvider' => $dataProvider
            ];
        }
        $this->status = ServiceDebt::STATUS_UNPAID;
        if(!empty($this->type)){
            $this->status = $this->type;
        }
        $building_area_ids = [];
        if(!empty($this->building_area_ids)){
            $building_area_ids = explode(',', $this->building_area_ids);
        }
        $targets = []; 
        if(!empty($this->targets)){
            $targets = explode(',', $this->targets);
        }else 
        $targets = ["1"];

        
        $serviceDebts = ServiceDebt::find()->where(['building_cluster_id' => $buildingCluster->id,'building_area_id' => $building_area_ids, 'status' => $this->status])->all();
        $serviceDebt_apartment_ids = []; 
        $serviceDebt_building_area_ids = []; 
        if(!empty($serviceDebts)){
            foreach($serviceDebts as $serviceDebt){
                $serviceDebt_apartment_ids[] = $serviceDebt->apartment_id ; 
                $serviceDebt_building_area_ids[] = $serviceDebt->building_area_id ; 
            }
        }
        if(empty($serviceDebts)){
            $query->where(
                '1 = 0'
            );
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'building_area_id' => $serviceDebt_building_area_ids,
        ]);
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'apartment_id' => $serviceDebt_apartment_ids,
            'early_debt' => $this->early_debt,
            'end_debt' => $this->end_debt,
            'receivables' => $this->receivables,
            'collected' => $this->collected,
            'month' => $this->month,
            'type' =>$targets,
            'status' => AnnouncementSendNewResponse::STATUS_ACTIVE,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        $dataCountRes = ServiceDebt::countTotalSend($buildingCluster->id, $this->status);

        return [
            'dataCount' => $dataCountRes,
            'dataProvider' => $dataProvider
        ];
    }

    private function exportPaymentFee($dataCountRes, $dataProvider)
    {
        $file_path = '/uploads/fee/'.'Danh sách công nợ.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách công nợ');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Chủ hộ');
        $sheet->setCellValue('C1', 'Căn hộ');
        $sheet->setCellValue('D1', 'Lô');
        $sheet->setCellValue('E1', 'Tầng');
        $sheet->setCellValue('F1', 'Nợ đầu kỳ (vnđ)');
        $sheet->setCellValue('G1', 'Phát sinh phải thu (vnđ)');
        $sheet->setCellValue('H1', 'Phát sinh đã thu (vnđ)');
        $sheet->setCellValue('I1', 'Nợ cuối kỳ (vnđ)');
        $sheet->setCellValue('J1', 'Tình trạng');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
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
            $sheet->setCellValue('B'.$i, ($item->apartment)?$item->apartment->resident_user_name:'');
            $sheet->setCellValue('C'.$i, ($item->apartment)?$item->apartment->name:'');
            $sheet->setCellValue('D'.$i, $lo);
            $sheet->setCellValue('E'.$i, $tang);
            $sheet->setCellValue('F'.$i, $item->early_debt);
            $sheet->getStyle('F'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('G'.$i, $item->receivables);
            $sheet->getStyle('G'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('H'.$i, $item->collected);
            $sheet->getStyle('H'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('I'.$i, $item->end_debt);
            $sheet->getStyle('I'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('J'.$i, (ServiceDebt::$status_lst[$item->status])?ServiceDebt::$status_lst[$item->status]:'');
            $i++;
        }
        $spreadsheet->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
        $sheet->setCellValue('A'.$i, 'Tổng (vnđ)');
        $sheet->setCellValue('F'.$i, $dataCountRes['early_debt']);
        $sheet->getStyle('F'.$i)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->setCellValue('G'.$i, $dataCountRes['receivables']);
        $sheet->getStyle('G'.$i)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->setCellValue('H'.$i, $dataCountRes['collected']);
        $sheet->getStyle('H'.$i)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->setCellValue('I'.$i, $dataCountRes['end_debt']);
        $sheet->getStyle('I'.$i)->getNumberFormat()->setFormatCode('#,##0');
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
}
