<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\models\Apartment;
use common\models\ServiceBill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ApartmentSearch represents the model behind the search form of `frontend\models\Apartment`.
 */
class ApartmentSearch extends Apartment
{
    public $status_delivery;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['form_type', 'total_members', 'date_received', 'date_delivery', 'id', 'status', 'capacity', 'building_area_id', 'resident_user_id', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by' , 'status_delivery'], 'integer'],
            [['handover', 'code', 'name', 'parent_path', 'resident_user_name', 'short_name'], 'safe'],
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
        $user = Yii::$app->user->getIdentity();
        $query = ApartmentResponse::find()->where(['building_cluster_id' => $user->building_cluster_id, 'is_deleted' => Apartment::NOT_DELETED]);

        // add conditions that should always apply here
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

        $this->load(CUtils::modifyParams($params),'');

        if (!$this->validate()) {
            Yii::error($this->errors);
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

//         grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'building_area_id' => $this->building_area_id,
            'resident_user_id' => $this->resident_user_id,
            'capacity' => $this->capacity,
            'date_received' => $this->date_received,
            'date_delivery' => $this->date_delivery,
            'total_members' => $this->total_members,
            'form_type' => $this->form_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'short_name', $this->short_name])
            ->andFilterWhere(['like', 'resident_name_search', CVietnameseTools::removeSigns2($this->resident_user_name)])
            ->andFilterWhere(['like', 'handover', $this->handover])
            ->andFilterWhere(['like', 'parent_path', $this->parent_path]);

        if(isset($this->status_delivery)){
            if($this->status_delivery == 0){
                $query->andWhere(['OR', ['date_delivery' => null], ['date_delivery' => '']]);
            }else if($this->status_delivery == 1){
                $query->andWhere(['NOT', ['date_delivery' => null]]);
            }
        }
        if($is_export == true){
            return self::exportApartment($dataProvider);
        }
        return $dataProvider;
    }

    private function exportApartment($dataProvider)
    {
        $file_path = '/uploads/fee/' . 'Danh sách bất động sản.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách bất động sản');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên bất động sản');
        $sheet->setCellValue('C1', 'Diện tích');
        $sheet->setCellValue('D1', 'Tình trạng bàn giao');
        $sheet->setCellValue('E1', 'Ngày bàn giao');
        $sheet->setCellValue('F1', 'Tình trạng ở');
        $sheet->setCellValue('G1', 'Số thành viên');
        $sheet->setCellValue('H1', 'Mã bất động sản');
        $sheet->setCellValue('I1', 'Loại bất động sản');
        $sheet->setCellValue('J1', 'Cấu trúc cấp 1');
        $sheet->setCellValue('K1', 'Cấu trúc cấp 2');
        $sheet->setCellValue('L1', 'Cấu trúc cấp 3');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
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
        foreach ($dataProvider->getModels() as $apartment) {
            $type_list = $apartment->getFormTypeList();
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $apartment->name);
            $sheet->setCellValue('C' . $i, $apartment->capacity);
            $sheet->setCellValue('D' . $i, !empty($apartment->date_delivery) ? 'Đã bàn giao' : 'Chưa bàn giao');
            $sheet->setCellValue('E' . $i, !empty($apartment->date_delivery) ? date('d/m/Y') : '');
            $sheet->setCellValue('F' . $i, Apartment::$status_list[$apartment->status] ?? '');
            $sheet->setCellValue('G' . $i, $apartment->total_members);
            $sheet->setCellValue('H' . $i, $apartment->code);
            $sheet->setCellValue('I' . $i, isset($type_list[$apartment->form_type]) ? $type_list[$apartment->form_type] : '');
            $path = explode('/', $apartment->parent_path);
            $sheet->setCellValue('J' . $i, $path[0] ?? '');
            $sheet->setCellValue('K' . $i, $path[1] ?? '');
            $sheet->setCellValue('L' . $i, $path[2] ?? '');
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path
        ];
    }
}
