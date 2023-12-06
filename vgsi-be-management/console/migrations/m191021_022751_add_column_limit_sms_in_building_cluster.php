<?php

use yii\db\Migration;

/**
 * Class m191021_022751_add_column_limit_sms_in_building_cluster
 */
class m191021_022751_add_column_limit_sms_in_building_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'limit_sms', $this->integer(11)->defaultValue(0)->comment('Giới hạn số lượng tin nhắn/1 tháng'));
        $this->addColumn('building_cluster', 'limit_email', $this->integer(11)->defaultValue(0)->comment('Giới hạn số lượng email/1 tháng'));
        $this->addColumn('building_cluster', 'limit_notify', $this->integer(11)->defaultValue(0)->comment('Giới hạn số lượng notify/1 tháng'));
        $this->createIndex( 'idx-building_cluster-limit_sms','building_cluster','limit_sms' );
        $this->createIndex( 'idx-building_cluster-limit_email','building_cluster','limit_email' );
        $this->createIndex( 'idx-building_cluster-limit_notify','building_cluster','limit_notify' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191021_022751_add_column_limit_sms_in_building_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191021_022751_add_column_limit_sms_in_building_cluster cannot be reverted.\n";

        return false;
    }
    */
}
