<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="DavisStatusForm")
 * )
 */
class DavisStatusForm extends Model
{
    /**
     * @SWG\Property(description="string")
     * @var string
     */
    public $data;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data'], 'safe']
        ];
    }

    public function status()
    {
        Yii::info($this->attributes);
        if(empty($this->data)){
            $this->data = [];
        }
//        $data_new = [];
//        foreach ($this->data as $row){
//            if($row['device_id'] == 'd000000000001'){
//                $data_new[] = $row;
//            }
//            $row['puri_trak_id'] = $row['id'];
//            unset($row['id']);
//            $device_id = $row['device_id'];
//            $hours = strtotime(date('Y-m-d H:00:00',strtotime($row['time'] . ' UTC')));
//            $puriTrakHistory = PuriTrakHistory::findOne(['device_id' => $device_id, 'hours' => $hours]);
//            if(empty($puriTrakHistory)){
//                $puriTrakHistory = new PuriTrakHistory();
//            }
//            $puriTrakHistory->load($row, '');
//            $puriTrakHistory->time = strtotime($puriTrakHistory->time . ' UTC');
//            $puriTrakHistory->hours = $hours;
//            if(!$puriTrakHistory->save()){
//                Yii::error($puriTrakHistory->errors);
//            }
//        }
//        if(empty($data_new)){
//            return [
//                'success' => true,
//                'message' => Yii::t('frontend', "Success"),
//            ];
//        }
//        /*
//         * chuyển tiếp bản tin xuống WEB/APP
//         * TODO: Tạm thời gửi cho tất cả các cluster, sau update cơ ché sync device thì update ở đây
//         */
//        $buildingClusters = BuildingCluster::find()->where(['is_deleted' => BuildingCluster::NOT_DELETED])->all();
//        $rooms = [];
//        foreach ($buildingClusters as $buildingCluster){
//            $rooms[] = 'building_cluster_' . $buildingCluster->id;
//        }
//
//        $socket = new SocketHelper();
//        $socket->of('/')->to($rooms)->flag('broadcast')->emit('puri_trak_status', ['payload' => $data_new]);
//        Yii::info($socket->res());
//        Yii::info("======================");
//        Yii::info($rooms);
        return [
            'success' => true,
            'message' => Yii::t('frontend', "Success"),
        ];
    }
}
