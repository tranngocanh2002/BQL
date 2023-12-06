<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\CVietnameseTools;
use common\models\ResidentUser;
use common\models\ServiceBill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ApartmentMapResidentUser;

/**
 * ApartmentMapResidentUserSearch represents the model behind the search form of `common\models\ApartmentMapResidentUser`.
 */
class ApartmentMapResidentUserSearch extends ApartmentMapResidentUser
{
    public $name;
    public $phone;
    public $email;
    public $total_apartment;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'apartment_id', 'total_apartment', 'resident_user_id', 'building_cluster_id', 'building_area_id', 'type', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'apartment_capacity', 'resident_user_gender', 'resident_user_birthday', 'install_app'], 'integer'],
            [['apartment_name', 'apartment_short_name', 'name', 'phone', 'email', 'resident_user_phone'], 'safe'],
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
        $query = ApartmentMapResidentUserResponse::find()->where(['building_cluster_id' => $user->building_cluster_id])->groupBy('resident_user_phone');
        $query2 = ApartmentMapResidentUserResponse::find()->where(['building_cluster_id' =>  $user->building_cluster_id])->groupBy('resident_user_phone');

        $arrayActive = [
            'query' => $query,
            'pagination' => [
                'pageSize' => 100000,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ];
        // add conditions that should always apply here
        if(isset($params['total_apartment']) && (int)$params['total_apartment'] == 0){
            $arrayActive = [
                'query' => $query2,
                'pagination' => [
                    'pageSize' => 100000,
                ],
                'sort' => [
                    'defaultOrder' => [
                        'id' => SORT_DESC,
                    ]
                ],
            ];
        }        

        if($is_export == false){
            $arrayActive['pagination'] = [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ];
        }
        $dataProvider = new ActiveDataProvider($arrayActive);

        $this->load(CUtils::modifyParams($params),'');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if(isset($params['total_apartment']))
        {
            $query->andFilterWhere(['is_deleted' => 0]);
        }
        if(isset($params['total_apartment']) && (int)$params['total_apartment'] == 0){
            $query2->andFilterWhere(['is_deleted' => 1]);
            $query2->andFilterWhere([
                'id' => $this->id,
                'apartment_id' => $this->apartment_id,
                'resident_user_id' => $this->resident_user_id,
                'type' => $this->type,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'created_by' => $this->created_by,
                'updated_by' => $this->updated_by,

            ]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'apartment_id' => $this->apartment_id,
            'resident_user_id' => $this->resident_user_id,
//            'building_cluster_id' => $this->building_cluster_id,
//            'building_area_id' => $this->building_area_id,
            'type' => $this->type,
//            'install_app' => $this->install_app,
//            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
//            'apartment_capacity' => $this->apartment_capacity,
//            'resident_user_gender' => $this->resident_user_gender,
//            'resident_user_birthday' => $this->resident_user_birthday,
        ]);

        
        if(isset($this->install_app) && $this->install_app == 1){
            $query->andWhere(['install_app' => $this->install_app]);
            $query2->andWhere(['install_app' => $this->install_app]);

        }else if(isset($this->install_app) && $this->install_app == 0){
            $query->andWhere(['or', ['install_app' => $this->install_app], ['install_app' => null]]);
            $query2->andWhere(['or', ['install_app' => $this->install_app], ['install_app' => null]]);
        }

        $query->andFilterWhere(['or',['like', 'resident_user_phone', $this->phone], ['like', 'resident_user_phone', preg_replace('/^0/', '84', $this->phone)]])
            ->andFilterWhere(['or',['like', 'resident_user_phone', $this->resident_user_phone], ['like', 'resident_user_phone', preg_replace('/^0/', '84', $this->resident_user_phone)]])
//            ->andFilterWhere(['like', 'resident_user_first_name', $this->name])
            ->andFilterWhere(['like', 'resident_name_search', $this->name])
//            ->andFilterWhere(['like', 'resident_name_search', CVietnameseTools::removeSigns2($this->name)])
            ->andFilterWhere(['like', 'apartment_name', $this->apartment_name])
            ->andFilterWhere(['like', 'apartment_short_name', $this->apartment_short_name]);
//        if(!empty($this->name)){
//            $this->name = preg_replace('/ /', '%', urldecode($this->name));
//            $query->andWhere("resident_user_first_name like '%$this->name%'");
//        }
        $query2->andFilterWhere(['or',['like', 'resident_user_phone', $this->phone], ['like', 'resident_user_phone', preg_replace('/^0/', '84', $this->phone)]])
            ->andFilterWhere(['or',['like', 'resident_user_phone', $this->resident_user_phone], ['like', 'resident_user_phone', preg_replace('/^0/', '84', $this->resident_user_phone)]])
//            ->andFilterWhere(['like', 'resident_user_first_name', $this->name])
            ->andFilterWhere(['like', 'resident_name_search', $this->name])
//            ->andFilterWhere(['like', 'resident_name_search', CVietnameseTools::removeSigns2($this->name)])
            ->andFilterWhere(['like', 'apartment_name', $this->apartment_name])
            ->andFilterWhere(['like', 'apartment_short_name', $this->apartment_short_name]);

        if(isset($params['total_apartment']) && $params['total_apartment'] > 0){
            $query->groupBy('resident_user_phone');
            if(isset($this->total_apartment)){
                $query->having('count(apartment_id) = ' . $this->total_apartment);
            }
        }else if(isset($params['total_apartment']) &&  $params['total_apartment'] == 0){
            $query->groupBy('resident_user_phone');         
            $apartments = $query->all(); 
            $phones = []; 
            foreach($apartments as $apartment){
                $phones[] = $apartment->resident_user_phone ; 
            }

            $query2->andFilterWhere(['NOT IN','resident_user_phone', $phones]);
            $query2->groupBy('resident_user_phone');
        }
    
        if($is_export == true){
            return self::exportResidentUser($dataProvider);
        }
        
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function listsByUser($params)
    {
        //o day cung lay ca bi xoa
        $user = Yii::$app->user->getIdentity();
        $query = ApartmentMapResidentUserResponse::find()->where(['building_cluster_id' =>  $user->building_cluster_id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['pageSize']) && $params['pageSize'] > 0 ? (int)$params['pageSize'] : 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'is_deleted' => SORT_ASC,
                    'created_at' => SORT_DESC,
                    'deleted_at' => SORT_DESC
                ]
            ],
        ]);

        $this->load(CUtils::modifyParams($params),'');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'apartment_id' => $this->apartment_id,
            'resident_user_id' => $this->resident_user_id,
            'type' => $this->type,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
//            'created_by' => $this->created_by,
//            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'resident_user_phone', $this->phone])
            ->andFilterWhere(['like', 'resident_user_phone', $this->resident_user_phone])
            ->andFilterWhere(['like', 'resident_name_search', CVietnameseTools::removeSigns2($this->name)])
            ->andFilterWhere(['like', 'apartment_name', $this->apartment_name]);

        return $dataProvider;
    }

    private function exportResidentUser($dataProvider)
    {
        $file_path = '/uploads/fee/' . 'Danh sách cư dân.xlsx';
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
        $sheet->setCellValue('J1', 'Số CMND/CCCD/Hộ chiếu');
        $sheet->setCellValue('K1', 'Ngày cấp');
        $sheet->setCellValue('L1', 'Nơi cấp');
        $sheet->setCellValue('M1', 'Quốc tịch');
        $sheet->setCellValue('N1', 'Số thị thực');
        $sheet->setCellValue('O1', 'Ngày hết hạn thị thực');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
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
        foreach ($dataProvider->getModels() as $apartmentMapResidentUser) {
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $apartmentMapResidentUser->resident_user_first_name);
            $resident_user_phone = $apartmentMapResidentUser->resident_user_phone;
            if(strlen($resident_user_phone) == 9)
            {
                $resident_user_phone = '84'.$resident_user_phone;
            }
            if(strlen($resident_user_phone) == 10)
            {
                $resident_user_phone = '84'.substr($resident_user_phone,1);
            }
            $sheet->setCellValue('C' . $i, $resident_user_phone);
            $sheet->setCellValue('D' . $i, $apartmentMapResidentUser->resident_user_email);
            $sheet->setCellValue('E' . $i, isset(ResidentUser::$gender_list[$apartmentMapResidentUser->resident_user_gender]) ? ResidentUser::$gender_list[$apartmentMapResidentUser->resident_user_gender] : '');

            $resident_user_birthday = $apartmentMapResidentUser->resident_user_birthday;
            $sheet->setCellValue('F'.$i, !empty($resident_user_birthday) ? date('d/m/Y', $resident_user_birthday) : '');
            $sheet->getStyle('F'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');

            $sheet->setCellValue('G' . $i, $apartmentMapResidentUser->work);

            $ngay_dang_ky_tam_chu = $apartmentMapResidentUser->ngay_dang_ky_tam_chu;
            $sheet->setCellValue('H'.$i, !empty($ngay_dang_ky_tam_chu) ? date('d/m/Y', $ngay_dang_ky_tam_chu) : '');
            $sheet->getStyle('H'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');

            $ngay_dang_ky_nhap_khau = $apartmentMapResidentUser->ngay_dang_ky_nhap_khau;
            $sheet->setCellValue('I'.$i, !empty($ngay_dang_ky_nhap_khau) ? date('d/m/Y', $ngay_dang_ky_nhap_khau) : '');
            $sheet->getStyle('I'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');

            $sheet->setCellValue('J' . $i, $apartmentMapResidentUser->cmtnd);
            $ngay_cap_cmtnd = $apartmentMapResidentUser->ngay_cap_cmtnd;
            $sheet->setCellValue('K'.$i, !empty($ngay_cap_cmtnd) ? date('d/m/Y', $ngay_cap_cmtnd) : '');
            $sheet->getStyle('K'.$i)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
            $sheet->setCellValue('L' . $i, $apartmentMapResidentUser->noi_cap_cmtnd);

            $nationality = 'Nước ngoài';
            if(empty($apartmentMapResidentUser->resident_user_nationality) || in_array(strtolower(trim($apartmentMapResidentUser->resident_user_nationality)), ['vn', 'vi'])){
                $nationality = 'Việt Nam';
            }
            $sheet->setCellValue('M' . $i, $nationality);

            $sheet->setCellValue('N' . $i, $apartmentMapResidentUser->so_thi_thuc);
            $ngay_het_han_thi_thuc = $apartmentMapResidentUser->ngay_het_han_thi_thuc;
            $sheet->setCellValue('O'.$i, !empty($ngay_het_han_thi_thuc) ? date('d/m/Y', $ngay_het_han_thi_thuc) : '');
            $sheet->getStyle('O'.$i)
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
