<?php

use yii\db\Migration;

/**
 * Class m200705_230753_add_provider_default
 */
class m200705_230753_add_provider_default extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $buildingClusters = \common\models\BuildingCluster::find()->where(['is_deleted' => \common\models\BuildingCluster::NOT_DELETED])->all();
        foreach ($buildingClusters as $buildingCluster){
            $providerCheck = \common\models\ServiceProvider::findOne(['building_cluster_id' => $buildingCluster->id, 'is_deleted' => \common\models\ServiceProvider::NOT_DELETED]);
            if(empty($providerCheck)){
                $provider = new \common\models\ServiceProvider();
                $provider->name = $buildingCluster->name;
                $provider->address = $buildingCluster->address;
                $provider->building_cluster_id = $buildingCluster->id;
                $provider->status = 1;
                $provider->using_bank_cluster = 1;
                if(!$provider->save()){
                    var_dump($provider->errors);
                    echo 'Import Error';
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200705_230753_add_provider_default cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200705_230753_add_provider_default cannot be reverted.\n";

        return false;
    }
    */
}
