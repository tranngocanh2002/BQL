<?php

namespace console\controllers;

use common\helpers\ApiHelper;
use common\helpers\CUtils;
use common\models\Job;
use Exception;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;


class JobController extends Controller
{
    /*
     * Kiểm tra công việc
     * Chạy định kỳ hàng ngày vào 07:00
     * chia ra nhiều luồng để chạy
     * $s : số luồng chạy đồng thời
     * $t : số dư phép chia mỗi luồng
     */
    public function actionCheck($s, $t)
    {
        echo "Job Check Start: " . date('Y-m-d H:i:s', time()) . "\n";
        $jobs = Job::find()->where(['status' => [Job::STATUS_NEW, Job::STATUS_DOING]])->andWhere("id%$s=$t");
        foreach ($jobs->each() as $job) {
            /**
             * @var $job Job
             */
            $job->count_expire = $job->diffDate();
            if($job->count_expire >= 9999){
                $job->time_end = $job->time_end + 24*60*60;
            }
            if(!$job->save()){
                echo "Job Check Error: $job->id \n";
            }
            if($job->count_expire == 1){
                echo "start send notify:  $job->id \n";
                if(!empty($job->performer)){
                    $arrPerformer = explode(',', $job->performer);
                    $job->sendNotifyToPerformer(Job::REMIND_WORK, $arrPerformer);
                }
            }
        }
        echo "Job Check End: " . date('Y-m-d H:i:s', time()) . "\n";
    }
}