<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\models\ServiceUtilityFree;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ServiceBookingReportWeek;

/**
 * ServiceBookingReportWeekSearch represents the model behind the search form of `common\models\ServiceBookingReportWeek`.
 */
class ServiceBookingReportWeekSearch extends ServiceBookingReportWeek
{
    public $start_date;
    public $end_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'date', 'status', 'building_cluster_id', 'service_map_management_id', 'service_utility_config_id', 'service_utility_free_id', 'total_price', 'created_at', 'updated_at'], 'integer'],
            [['start_date', 'end_date'], 'safe']
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
    public function search($params)
    {
        $buildingCluster = Yii::$app->building->BuildingCluster;
        $query = ServiceBookingReportWeek::find()
            ->where(['building_cluster_id' => $buildingCluster->id, 'status' => ServiceBookingReportWeek::STATUS_PAID]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
//            'id' => $this->id,
//            'date' => $this->date,
            'status' => $this->status,
//            'building_cluster_id' => $this->building_cluster_id,
//            'service_map_management_id' => $this->service_map_management_id,
//            'service_utility_config_id' => $this->service_utility_config_id,
            'service_utility_free_id' => $this->service_utility_free_id,
//            'total_price' => $this->total_price,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ]);



        if(empty($this->start_date)){
            $this->start_date = time();
        }
        if(empty($this->end_date)){
            $this->end_date = time();
        }

        $start_month = strtotime(date('Y-m-d 00:00:00', $this->start_date));
        $start_date = CUtils::startTimeWeek($this->start_date); // đầu tuần
        $end_date = CUtils::startTimeNextWeek($this->end_date); // đầu tuần tiếp theo

        Yii::info(date('d-m-Y', $start_date));
        Yii::info(date('d-m-Y', $end_date));

        $query->andWhere(['>=', 'date', $start_date]);
        $query->andWhere(['<=', 'date', $end_date]);

        $bookByDates = [];
        $serviceFreeIds = [];
        foreach ($dataProvider->getModels() as $serviceBookReport) {
            $serviceFreeIds[$serviceBookReport->service_utility_free_id] = $serviceBookReport->service_utility_free_id;
            $timeBooks = [
                'date' => $serviceBookReport->date,
                'service_utility_free_id' => $serviceBookReport->service_utility_free_id,
                'service_utility_free_name' => '',
                'total_price' => $serviceBookReport->total_price,
            ];
            $serviceFree = ServiceUtilityFree::findOne($serviceBookReport->service_utility_free_id);
            if(!empty($serviceFree)){
                $timeBooks['service_utility_free_name'] = $serviceFree->name;
            }

            $bookByDates[$serviceBookReport->date][] = $timeBooks;
        }
        Yii::info($bookByDates);
        $dates = [];
        $date_time_next = $start_date;

        $serviceUtilityFrees = ServiceUtilityFree::find()->where(['id' => $serviceFreeIds])->all();
        $allServiceNull = [];
        foreach ($serviceUtilityFrees as $serviceUtilityFree){
            $allServiceNull[$serviceUtilityFree->id] = [
                'date' => 0,
                'service_utility_free_id' => $serviceUtilityFree->id,
                'service_utility_free_name' => $serviceUtilityFree->name,
                'total_price' => 0,
            ];
        }
        Yii::info($allServiceNull);
//      {
//          "date": "integer",
//          "services": [
//              {
//                  "date": "integer",
//                  "service_utility_free_id": "integer",
//                  "service_utility_free_name": "integer"
//                  "total_price": "integer"
//              }
//          ]
//      }
        $i = 0;
        while ($date_time_next < $end_date) {
            Yii::info(date('d-m-Y', $date_time_next));
            $i++;
            if($date_time_next > $start_month){
                $allServiceNullNew = [];
                foreach ($allServiceNull as $id => $it){
                    $it['date'] = $date_time_next;
                    $allServiceNullNew[$id] = $it;
                }
                if (isset($bookByDates[$date_time_next])) {
                    foreach ($bookByDates[$date_time_next] as $items) {
                        if(isset($items['service_utility_free_id'])){
                            if(isset($allServiceNullNew[$items['service_utility_free_id']])){
                                $items['service_utility_free_name'] = $allServiceNullNew[$items['service_utility_free_id']]['service_utility_free_name'];
                                $allServiceNullNew[$items['service_utility_free_id']] = $items;
                            }
                        }
                    }
                }

                $dates[$date_time_next] = [
                    'date' => $date_time_next,
                    'services' => array_values($allServiceNullNew)
                ];
            }
            $date_time_next = CUtils::startTimeNextWeek($date_time_next);
            Yii::info(date('d-m-Y', $date_time_next));
            if ($i >= 500) {
                break;
            }
        }
        return array_values($dates);
    }
}
