<?php
namespace frontend\components;

use common\helpers\ApiHelper;
use common\models\BuildingCluster;
use yii\web\UnauthorizedHttpException;
use Yii;

/**
 * Created by PhpStorm.
 * User: qhuy.duong@gmail.com
 * Date: 21/05/2019
 * Time: 11:42 CH
 */
class BuildingComponent extends \yii\base\Component
{
    /**
     * @var \common\models\BuildingCluster
     */
    private $_building_cluster;

    public function getBuildingCluster(){
        if(Yii::$app->controller->id == 'callback'){
            return true;
        };
        if($this->_building_cluster) return $this->_building_cluster;
        $domainOrigin = ApiHelper::getDomainOrigin();
        if(empty($domainOrigin)){
            throw new UnauthorizedHttpException('Missing Domain Origin');
        }
        //Get building cluster
        $this->_building_cluster = BuildingCluster::findOne(['domain' => $domainOrigin, 'is_deleted' => BuildingCluster::NOT_DELETED, 'status' => BuildingCluster::STATUS_ACTIVE]);
        if(empty($this->_building_cluster)){
            throw new UnauthorizedHttpException('Domain không tồn tại trong hệ thống');
        }
        return $this->_building_cluster;
    }
}
