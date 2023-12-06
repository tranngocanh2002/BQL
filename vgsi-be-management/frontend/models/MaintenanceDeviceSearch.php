<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\models\MaintenanceDevice;
use common\models\ManagementUser;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PuriTrakHistorySearch represents the model behind the search form of `common\models\MaintenanceDeviceSearch`.
 */
class MaintenanceDeviceSearch extends MaintenanceDevice
{
    public $start_time;
    public $end_time;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'status', 'type', 'start_time', 'end_time'], 'safe'],
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
        $user = \Yii::$app->user->getIdentity();
        $query = MaintenanceDeviceResponse::find()->where([
            'building_cluster_id' => $user->building_cluster_id,
            'is_deleted' => MaintenanceDevice::NOT_DELETED
        ]);
        $arrayActive = [
            'query' => $query,
            'pagination' => [
                'pageSize' => 10000,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
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
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);
        $query->andFilterWhere([
            'status' => $this->status,
            'type' => $this->type,
        ]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'code', $this->code]);
        if($is_export == true){
            return self::exportFile($dataProvider);
        }
        return $dataProvider;
    }

    private function exportFile($dataProvider)
    {
        $file_path = '/uploads/fee/' . 'Danh sách thiết bị.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách thiết bị');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Mã thiết bị');
        $sheet->setCellValue('C1', 'Tên thiết bị');
        $sheet->setCellValue('D1', 'Loại thiết bị');
        $sheet->setCellValue('E1', 'Ngày bắt đầu bảo trì');
        $sheet->setCellValue('F1', 'Trạng thái');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F'];
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
        foreach ($dataProvider->getModels() as $maintenanceDevice) {
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $maintenanceDevice->code);
            $sheet->setCellValue('C' . $i, $maintenanceDevice->name);
            $sheet->setCellValue('D' . $i, MaintenanceDevice::$type_list[$maintenanceDevice->type] ?? MaintenanceDevice::$type_list[$maintenanceDevice->type]);
            $sheet->setCellValue('E' . $i, !empty($maintenanceDevice->maintenance_time_start) ? date('d/m/Y', $maintenanceDevice->maintenance_time_start) : '');
            $sheet->setCellValue('F' . $i, MaintenanceDevice::$status_list[$maintenanceDevice->status] ?? MaintenanceDevice::$status_list[$maintenanceDevice->status]);
            $i++;
        }
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
    public function searchSchedule($params, $is_export = false)
    {
        $user = \Yii::$app->user->getIdentity();
        $query = MaintenanceDeviceResponse::find()->where([
            'building_cluster_id' => $user->building_cluster_id,
            'is_deleted' => MaintenanceDevice::NOT_DELETED,
            'status' => MaintenanceDevice::STATUS_ON,
        ]);
        $arrayActive = [
            'query' => $query,
            'pagination' => [
                'pageSize' => 10000,
            ],
            'sort' => [
                'defaultOrder' => [
                    'maintenance_time_next' => SORT_DESC,
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
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);
        $query->andFilterWhere([
            'type' => $this->type,
        ]);

        $end_time = time();
        $start_time = strtotime("-2 month", $end_time);
        if($this->start_time){ $start_time = $this->start_time; }
        if($this->end_time){ $end_time = $this->end_time; }

        $returnTimeMains = [];
        $retunTimesMainNextId = [];
        $retunTimesMainLastId = [];
        $retunTimes = MaintenanceDeviceResponse::find()->where([
            'building_cluster_id' => $user->building_cluster_id,
            'is_deleted' => MaintenanceDevice::NOT_DELETED,
            'status' => MaintenanceDevice::STATUS_ON,
        ])
        ->andWhere(['or',
            ['between', 'maintenance_time_next', $start_time, $end_time],
            ['between', 'maintenance_time_last', $start_time, $end_time]
        ])
        ->all();
        foreach ($retunTimes as $retunTime) {
            $returnTimeMains[] = $retunTime->maintenance_time_last;
            $retunTimesMainLastId[] = $retunTime->id;
        }
        
        foreach ($returnTimeMains as $returnTimeMain) {
            if ($returnTimeMain == null) {
                $retunTimesMainNexts = MaintenanceDeviceResponse::find()->where([
                    'between', 'maintenance_time_next', $start_time, $end_time
                ])->all();
            // } else {
            //     $retunTimesMainLasts = MaintenanceDeviceResponse::find()
            //         ->andWhere(['between', 'maintenance_time_last', $start_time, $end_time])
            //         ->all();
            }
        }
        if (!empty($retunTimesMainNexts)) {
            foreach ($retunTimesMainNexts as $retunTimesMainNext) {
                $retunTimesMainNextId[] = $retunTimesMainNext->id;
            }
        }

        // if (!empty($retunTimesMainLasts)) {
        //     foreach ($retunTimesMainLasts as $retunTimesMainLast) {
        //         $retunTimesMainLastId[] = $retunTimesMainLast->id;
        //     }
        // }

        // $query->andFilterWhere(['>=', 'maintenance_time_next', $start_time]);
        // $query->andFilterWhere(['<=', 'maintenance_time_next', $end_time]);
        $query->andWhere([
            'or',
            [
                'and',
                ['not', ['maintenance_time_last' => null]],
                ['id' => $retunTimesMainLastId]
            ],
            [
                'and',
                ['maintenance_time_last' => null],
                ['id' => $retunTimesMainNextId]
            ],
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'code', $this->code]);
        if($is_export == true){
            return self::exportFileSchedule($dataProvider);
        }
        return $dataProvider;
    }

    private function exportFileSchedule($dataProvider)
    {
        $file_path = '/uploads/fee/' . 'Danh sách lịch bảo trì.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách lịch bảo trì');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Mã thiết bị');
        $sheet->setCellValue('C1', 'Tên thiết bị');
        $sheet->setCellValue('D1', 'Loại thiết bị');
        $sheet->setCellValue('E1', 'Ngày bảo trì gần nhất');
        $sheet->setCellValue('F1', 'Ngày bảo trì sắp tới');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F'];
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
        foreach ($dataProvider->getModels() as $maintenanceDevice) {
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $maintenanceDevice->code);
            $sheet->setCellValue('C' . $i, $maintenanceDevice->name);
            $sheet->setCellValue('D' . $i, MaintenanceDevice::$type_list[$maintenanceDevice->type] ?? MaintenanceDevice::$type_list[$maintenanceDevice->type]);
            $sheet->setCellValue('E' . $i, !empty($maintenanceDevice->maintenance_time_last) ? date('d/m/Y', $maintenanceDevice->maintenance_time_last) : '');
            $sheet->setCellValue('F' . $i, !empty($maintenanceDevice->maintenance_time_next) ? date('d/m/Y', $maintenanceDevice->maintenance_time_next) : '');
            $i++;
        }
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
    public function searchMaintainEquipment($params)
    {
        $user = \Yii::$app->user->getIdentity();
    
         // Lấy thời gian bắt đầu của khoảng 6 tháng gần nhất
        $sixMonthsAgoStart = strtotime('-5 months', strtotime(date('Y-m-01')));
        
        // Lấy thời gian kết thúc của tháng hiện tại
        $currentMonthEnd = strtotime(date('Y-m-t 23:59:59'));
        
        $monthlyCounts = [];
        $index = 0;
        if(!empty($params['from_month']) && !empty($params['to_month']))
        {
            $monthStart = $params['from_month'];
            $monthEnd   = $params['to_month'];
            $sixMonthsAgoStart = $params['from_month'];
            $currentMonthEnd   = $params['to_month'];
            $sixMonthsAgoStart = date('d/m/Y', $sixMonthsAgoStart);
            list($day, $month, $year) = explode('/', $sixMonthsAgoStart);
            if ($day == '31') {
                $day = '28';
                $dateString  = "$day/$month/$year";
                $dateFormat = 'd/m/Y';
                $dateTime = \DateTime::createFromFormat($dateFormat, $dateString);
                $sixMonthsAgoStart = $dateTime->getTimestamp();
            } else {
                $dateString  = "$day/$month/$year";
                $dateFormat = 'd/m/Y';
                $dateTime = \DateTime::createFromFormat($dateFormat, $dateString);
                $sixMonthsAgoStart = $dateTime->getTimestamp();
            }
            // Đếm số lượng bản ghi thỏa mãn điều kiện cho tháng hiện tại
            for ($i = $sixMonthsAgoStart; $i <= $currentMonthEnd; $i = strtotime('+1 month', $i)) 
            {
                $index += 1;
                $startTimestamp = strtotime('first day of', $i);
                $monthStart = strtotime(date('Y-m-d', $startTimestamp));
                $endTimestamp   = strtotime('last day of', $i);
                $monthEnd = strtotime(date('Y-m-d', $endTimestamp) . ' 23:59:58');
            
                $fix = ReportCountMaintainEquipmentResponse::find()
                    ->where([
                        'building_cluster_id' => $user->building_cluster_id,
                        'is_deleted' => MaintenanceDevice::NOT_DELETED,
                        'status' => MaintenanceDevice::STATUS_ON,
                    ])
                    // ->andWhere(['>', 'maintenance_time_last', $monthStart])
                    // ->andWhere(['<', 'maintenance_time_last', $monthEnd])
                    // ->andWhere(['between', 'COALESCE(maintenance_time_last, :monthStart)',$monthStart, $monthEnd])
                    ->andWhere(['between', 'maintenance_time_last', $monthStart, $monthEnd])
                    // ->params([':monthStart' => strtotime('-1 month', $i)])
                    ->count();
                $notFix = ReportCountMaintainEquipmentResponse::find()
                ->where([
                    'building_cluster_id' => $user->building_cluster_id,
                    'is_deleted' => MaintenanceDevice::NOT_DELETED,
                    'status' => MaintenanceDevice::STATUS_ON,
                ])
                // ->andWhere(['>', 'maintenance_time_next', 'maintenance_time_last'])
                // ->andWhere(['between', 'maintenance_time_next', $monthStart, $monthEnd])
                // ->andWhere(['between', 'COALESCE(maintenance_time_next, :monthEnd)', $monthStart, $monthEnd])
                // ->params([':monthEnd' => $monthEnd])
                ->andWhere(['between', 'maintenance_time_next', $monthStart, $monthEnd])
                ->count();
                
                
                // Lưu số lượng bản ghi vào mảng theo tháng
                $monthlyCounts[$index]['month']   = strtotime(date('Y-m', $i));
                $monthlyCounts[$index]['fix']     = $fix ?? 0;
                $monthlyCounts[$index]['not_fix'] = $notFix ?? 0;
            }

            return $monthlyCounts;
        }
        // if(!empty($params['from_month']) && empty($params['to_month']))
        // {
        //     $monthStart = $params['from_month'];
        //     $monthEnd   =  strtotime(date('Y-m-01 00:00:00', $monthStart) . " +6 months");
        //     $sixMonthsAgoStart = $monthStart;
        //     $currentMonthEnd   = $monthEnd;
        //     // Đếm số lượng bản ghi thỏa mãn điều kiện cho tháng hiện tại
        //     for ($i = $sixMonthsAgoStart; $i <= $currentMonthEnd; $i = strtotime('+1 month', $i)) {

        //         $monthStart = $i;
        //         $monthEnd = strtotime('last day of', $i);
            
        //         $fix = ReportCountMaintainEquipmentResponse::find()
        //             ->where([
        //                 'building_cluster_id' => $user->building_cluster_id,
        //                 'is_deleted' => MaintenanceDevice::NOT_DELETED,
        //                 'status' => MaintenanceDevice::STATUS_ON,
        //             ])
        //             ->andWhere(['between', 'maintenance_time_last', $monthStart, $monthEnd])
        //             ->count();
                
        //         $notFix = ReportCountMaintainEquipmentResponse::find()
        //         ->where([
        //             'building_cluster_id' => $user->building_cluster_id,
        //             'is_deleted' => MaintenanceDevice::NOT_DELETED,
        //             'status' => MaintenanceDevice::STATUS_ON,
        //         ])
        //         ->andWhere(['between', 'maintenance_time_next', $monthStart, $monthEnd])
        //         ->count();
                
                
                
        //         // Lưu số lượng bản ghi vào mảng theo tháng
        //         $monthlyCounts[date('Y-m', $i)]['month']   = date('Y-m', $i);
        //         $monthlyCounts[date('Y-m', $i)]['fix']     = $fix ?? 0;
        //         $monthlyCounts[date('Y-m', $i)]['not_fix'] = $notFix ?? 0;
        //     }
        //         return $monthlyCounts;
        // }
        // if(empty($params['from_month']) && !empty($params['to_month']))
        // {
        //     $monthEnd   =  $params['to_month'];
        //     $monthStart = strtotime(date('Y-m-01 00:00:00', $monthEnd) . " -6 months");
        //     $sixMonthsAgoStart = $monthStart;
        //     $currentMonthEnd   = $monthEnd;
        //     // Đếm số lượng bản ghi thỏa mãn điều kiện cho tháng hiện tại
        //     for ($i = $sixMonthsAgoStart; $i <= $currentMonthEnd; $i = strtotime('+1 month', $i)) {

        //         $monthStart = $i;
        //         $monthEnd = strtotime('last day of', $i);
        //         // Đếm số lượng bản ghi thỏa mãn điều kiện cho tháng hiện tại
        //         $fix = ReportCountMaintainEquipmentResponse::find()
        //             ->where([
        //                 'building_cluster_id' => $user->building_cluster_id,
        //                 'is_deleted' => MaintenanceDevice::NOT_DELETED,
        //                 'status' => MaintenanceDevice::STATUS_ON,
        //             ])
        //             ->andWhere(['between', 'maintenance_time_last', $monthStart, $monthEnd])
        //             ->count();
                
        //         $notFix = ReportCountMaintainEquipmentResponse::find()
        //         ->where([
        //             'building_cluster_id' => $user->building_cluster_id,
        //             'is_deleted' => MaintenanceDevice::NOT_DELETED,
        //             'status' => MaintenanceDevice::STATUS_ON,
        //         ])
        //         ->andWhere(['between', 'maintenance_time_next', $monthStart, $monthEnd])
        //         ->count();
                
        //         // Lưu số lượng bản ghi vào mảng theo tháng
        //         $monthlyCounts[date('Y-m', $i)]['month']   = date('Y-m', $i);
        //         $monthlyCounts[date('Y-m', $i)]['fix']     = $fix ?? 0;
        //         $monthlyCounts[date('Y-m', $i)]['not_fix'] = $notFix ?? 0;
        //     }
        //         return $monthlyCounts;
        // }
       
        // Lặp qua từng tháng trong khoảng thời gian 6 tháng gần nhất
        for ($i = $sixMonthsAgoStart; $i <= $currentMonthEnd; $i = strtotime('+1 month', $i)) {
            $monthStart = $i;
            $monthEnd = strtotime('last day of', $i);
            
            // Đếm số lượng bản ghi thỏa mãn điều kiện cho tháng hiện tại
            $fix = ReportCountMaintainEquipmentResponse::find()
                ->where([
                    'building_cluster_id' => $user->building_cluster_id,
                    'is_deleted' => MaintenanceDevice::NOT_DELETED,
                    'status' => MaintenanceDevice::STATUS_ON,
                ])
                ->andWhere(['between', 'maintenance_time_last', $monthStart, $monthEnd])
                ->count();
            if($monthStart === strtotime(date('Y-m-01 00:00:00')))
            {
                $notFix = ReportCountMaintainEquipmentResponse::find()
                ->where([
                    'building_cluster_id' => $user->building_cluster_id,
                    'is_deleted' => MaintenanceDevice::NOT_DELETED,
                    'status' => MaintenanceDevice::STATUS_ON,
                ])
                ->andWhere(['between', 'maintenance_time_next', $monthStart, $monthEnd])
                ->count();
            }
            
            
            // Lưu số lượng bản ghi vào mảng theo tháng
            $monthlyCounts[date('Y-m', $i)]['month']   = strtotime(date('Y-m', $i));
            $monthlyCounts[date('Y-m', $i)]['fix']     = $fix ?? 0;
            $monthlyCounts[date('Y-m', $i)]['not_fix'] = $notFix ?? 0;
        }
        return $monthlyCounts;
    }
}
