<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\models\ManagementUser;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ManagementUserSearch represents the model behind the search form of `frontend\models\ManagementUser`.
 */
class ManagementUserSearch extends ManagementUser
{
    public $name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'is_deleted', 'created_at', 'updated_at', 'auth_group_id'], 'integer'],
            [['email', 'name'], 'safe'],
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
        $query = ManagementUserResponse::find()->where([
            'role_type' => ManagementUser::DEFAULT_ADMIN,
            'is_deleted' => ManagementUser::NOT_DELETED,
            'building_cluster_id' => $user->building_cluster_id
        ]);

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

        $this->load(CUtils::modifyParams($params),'');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'auth_group_id' => $this->auth_group_id,
            'code_management_user' => $this->code_management_user,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'first_name', $this->name]);
        if($is_export == true){
            return self::exportFile($dataProvider);
        }
        return $dataProvider;
    }

    private function exportFile($dataProvider)
    {
        $file_path = '/uploads/fee/' . 'Danh sách nhân sự.xlsx';
        $fileandpath = \Yii::getAlias('@webroot') . $file_path;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách nhân sự');
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Mã nhân viên');
        $sheet->setCellValue('C1', 'Họ và Tên');
        $sheet->setCellValue('D1', 'Giới tính');
        $sheet->setCellValue('E1', 'Ngày sinh');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Số điện thoại');
        $sheet->setCellValue('H1', 'Nhóm quyền');

        $arrColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G','H'];
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
        foreach ($dataProvider->getModels() as $managementUser) {
            $sheet->setCellValue('A' . $i, $i - 1);
            $sheet->setCellValue('B' . $i, $managementUser->code_management_user ?? "");
            $sheet->setCellValue('C' . $i, $managementUser->first_name .' '. $managementUser->last_name);
            $sheet->setCellValue('D' . $i, ManagementUser::$gender_list[$managementUser->gender] ?? ManagementUser::$gender_list[ManagementUser::GENDER_1]);
            $sheet->setCellValue('E' . $i, !empty($managementUser->birthday) ? date('d/m/Y', $managementUser->birthday) : '');
            $sheet->setCellValue('F' . $i, $managementUser->email);
            $phone = preg_replace("/^84/", '0', $managementUser->phone);
            if ($phone[0] === '0') {
                $phone = '84' . substr($phone, 1); // Cắt bỏ số 0 và nối số 84 vào đầu chuỗi
            }
            $sheet->setCellValue('G' . $i, $phone );
            $sheet->setCellValue('H' . $i, !empty($managementUser->authGroup) ? $managementUser->authGroup->name : '');
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileandpath);
        return [
            'file_path' => $file_path
        ];
    }
}
