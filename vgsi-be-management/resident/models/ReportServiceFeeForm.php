<?php

namespace resident\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\ApartmentMapResidentUser;
use common\models\ServiceMapManagement;
use common\models\ServicePaymentFee;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ReportServiceFeeForm")
 * )
 */
class ReportServiceFeeForm extends Model
{
    public $apartment_id;
    public $month;
    public $start_month;
    public $end_month;
    public $service_map_management_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apartment_id'], 'required'],
            [['month'], 'required', "on" => ['byMonth']],
            [['start_month', 'end_month'], 'required', "on" => ['byRangeMonth']],
            [['apartment_id', 'service_map_management_id', 'month', 'start_month', 'end_month'], 'integer'],
        ];
    }

    /**
     * @SWG\Property(property="total_fee", type="integer", description="tổng phí"),
     * @SWG\Property(property="total_more_collecte", type="integer", description="tổng phí cần thanh toán"),
     * @SWG\Property(property="count_by_type_fee", type="array", description="tổng theo loại dịch vụ",
     *      @SWG\Items(type="object",
     *          @SWG\Property(property="id", type="integer"),
     *          @SWG\Property(property="color", type="string", description="màu sắc"),
     *          @SWG\Property(property="name", type="string", description="tên loại dịch vụ"),
     *          @SWG\Property(property="name_en", type="string", description="tên loại dịch vụ"),
     *          @SWG\Property(property="total_fee", type="integer", description="tổng phí của dich vụ"),
     *      ),
     * ),
     */
    public function byMonth()
    {
        $month = (!empty($this->month)) ? (int)$this->month : time();
        $startDay = strtotime(date('Y-m-01 00:00:00', $month));
        $endDay = strtotime(date('Y-m-01 00:00:00', strtotime("+1 month", $month)));
        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
        $sql = Yii::$app->db;
        $countByService = $sql->createCommand("select service_map_management_id, sum(price) as price, sum(more_money_collecte) as more_money_collecte from service_payment_fee where building_cluster_id = $building_cluster_id and apartment_id = $this->apartment_id and fee_of_month >= $startDay and fee_of_month < $endDay group by service_map_management_id")->queryAll();
        $countMoreCollecte = ServicePaymentFee::find()->select(["SUM(more_money_collecte) as more_money_collecte"])->where(['building_cluster_id' => $building_cluster_id, 'apartment_id' => $this->apartment_id, 'status' => ServicePaymentFee::STATUS_UNPAID])->one();
        $res = [
            'total_fee' => 0,
            'total_more_collecte' => !empty($countMoreCollecte) ? (int)$countMoreCollecte->more_money_collecte : 0,
            'count_by_type_fee' => []
        ];
        $count_by_type_fee = [];
        foreach ($countByService as $row){
            $res['total_fee'] += (int)$row['price'];
//            $res['total_more_collecte'] += (int)$row['more_money_collecte'];
            if(empty($count_by_type_fee[$row['service_map_management_id']])){
                $serviceMap = ServiceMapManagement::findOne(['id' => $row['service_map_management_id']]);
                $count_by_type_fee[$row['service_map_management_id']] = [
                    'id' => $serviceMap->id,
                    'color' => $serviceMap->color,
                    'name' => $serviceMap->service_name,
                    'name_en' => $serviceMap->service_name_en,
                    'total_fee' => (int)$row['price'],
                ];
            }else{
                $count_by_type_fee[$row['service_map_management_id']]['total_fee'] += (int)$row['price'];
            }
        }

        foreach($count_by_type_fee as $i => $item) {
            $res['count_by_type_fee'][] = $item;
        }

        return $res;
    }

    public function byRangeMonth()
    {
        $start_month = (!empty($this->start_month)) ? (int)$this->start_month : time();
        $end_month = (!empty($this->end_month)) ? (int)$this->end_month : time();

        $startMonth = strtotime(date('Y-m-01 00:00:00', $start_month));
        $endMonth = strtotime(date('Y-m-01 00:00:00', strtotime("+1 month", $end_month)));

        $user = Yii::$app->user->getIdentity();
        $apartmentMapResidentUser = ApartmentMapResidentUser::findOne(['apartment_id' => $this->apartment_id, 'resident_user_phone' => $user->phone, 'is_deleted' => ApartmentMapResidentUser::NOT_DELETED]);
        if(empty($apartmentMapResidentUser)){
            return [
                'success' => false,
                'message' => Yii::t('resident', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
            ];
        }
        $building_cluster_id = $apartmentMapResidentUser->building_cluster_id;
        $sql = Yii::$app->db;
        $where_str = " where building_cluster_id = $building_cluster_id and apartment_id = $this->apartment_id and fee_of_month >= $startMonth and fee_of_month < $endMonth ";
        if(!empty($this->service_map_management_id)){
            $where_str .= " and service_map_management_id = $this->service_map_management_id ";
        }
        $countByService = $sql->createCommand("select FROM_UNIXTIME(fee_of_month, '%Y-%m') as fee_of_month, sum(price) as price from service_payment_fee $where_str group by FROM_UNIXTIME(fee_of_month, '%Y-%m') order by FROM_UNIXTIME(fee_of_month, '%Y-%m') DESC")->queryAll();
        $res = [];
        foreach ($countByService as $row){
            $res[strtotime($row['fee_of_month'].'-01')] = [
                'month_str' => $row['fee_of_month'],
                'month' => strtotime($row['fee_of_month'].'-01'),
                'total_fee' => (int)$row['price'],
            ];
        }
        $start = $startMonth;
        $dataRes = [];
        while(true){
            if(empty($res[$start])){
                $res[$start] = [
                    'month_str' => date('Y-m', $start),
                    'month' => $start,
                    'total_fee' => 0,
                ];
            }
            $dataRes[] = $res[$start];
            $start = strtotime("+1 month", $start);
            if($start >= $endMonth){
                break;
            }
        }
        return $dataRes;
    }
}
