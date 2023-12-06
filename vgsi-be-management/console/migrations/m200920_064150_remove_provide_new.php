<?php

use yii\db\Migration;

/**
 * Class m200920_064150_remove_provide_new
 */
class m200920_064150_remove_provide_new extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $buildingClusters = \common\models\BuildingCluster::find()->where(['is_deleted' => \common\models\BuildingCluster::NOT_DELETED])->all();
        foreach ($buildingClusters as $buildingCluster){
            $providers = \common\models\ServiceProvider::find()->where(['building_cluster_id' => $buildingCluster->id, 'is_deleted' => \common\models\ServiceProvider::NOT_DELETED])->all();
            if(count($providers) > 1){
                $i = 0;
                foreach ($providers as $provider){
                    if($i == 0){
                        \common\models\ServiceMapManagement::updateAll(['service_provider_id' => $provider->id], ['building_cluster_id' => $buildingCluster->id]);
                    }else if($i >= 1){
                        if(!$provider->delete()){
                            Yii::error($provider->errors);
                        }
                    }
                    $i++;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200920_064150_remove_provide_new cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200920_064150_remove_provide_new cannot be reverted.\n";

        return false;
    }
    */
}
