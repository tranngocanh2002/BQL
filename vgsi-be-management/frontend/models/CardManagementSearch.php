<?php

namespace frontend\models;

use common\helpers\CUtils;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CardManagement;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;

/**
 * CardManagementSearch represents the model behind the search form of `common\models\CardManagement`.
 */
class CardManagementSearch extends CardManagement
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'id', 'building_cluster_id', 'apartment_id', 'resident_user_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['number','code','description','description_en','reason'], 'safe'],
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
    public function search($params,$is_export = false)
    {
        $buildingCluster = \Yii::$app->building->BuildingCluster;
        $query = CardManagementResponse::find()->where(['building_cluster_id' => $buildingCluster->id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ]
            ],
        ]);

        $this->load(CUtils::modifyParams($params), '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'building_cluster_id' => $this->building_cluster_id,
            'apartment_id' => $this->apartment_id,
            'resident_user_id' => $this->resident_user_id,
            'status' => $this->status,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'code' => $this->code,
            'reason' => $this->reason
        ]);

        $query->andFilterWhere(['like', 'number', $this->number]);
        if($is_export == true){
            return self::exportApartment($dataProvider);
        }
        return $dataProvider;
    }
    private function exportApartment($dataProvider)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $file_path = '/uploads/fee/' . 'Danh sách thẻ hợp nhất.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách thẻ hợp nhất');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Mã thẻ');
        $sheet->setCellValue('C1', 'Số thẻ');
        $sheet->setCellValue('D1', 'Trạng thái');
        $sheet->setCellValue('E1', 'BĐS');
        $sheet->setCellValue('F1', 'Chủ thẻ');

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
        $cardManagements = CardManagement::find()->where(['building_cluster_id' => $buildingCluster->id])->orderBy(['code' => SORT_ASC])->all();
        foreach ($cardManagements as $cardManagement) {
            $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['id' => $cardManagement->resident_user_id, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
            $apartment = Apartment::findOne(['id' => $cardManagement->apartment_id, 'is_deleted' => Apartment::NOT_DELETED]);
            // $type_list = $cardManagement->getFormTypeList();
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $cardManagement->code);
            $sheet->setCellValue('C' . $i, $cardManagement->number);
            $sheet->setCellValue('D' . $i, CardManagement::$status_list[$cardManagement->status] ?? '');
            if (!empty($apartment)) {
                $sheet->setCellValue('E' . $i, $apartment->name);
            }
            if (!empty($apartment)) {
                $sheet->setCellValue('F' . $i, $apartmentMapResidentUser->resident_user_first_name . ' ' . $apartmentMapResidentUser->resident_user_last_name);
            }
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path
        ];
    }
}
