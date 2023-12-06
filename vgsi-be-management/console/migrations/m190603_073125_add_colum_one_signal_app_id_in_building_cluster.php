<?php

use yii\db\Migration;

/**
 * Class m190603_073125_add_colum_one_signal_app_id_in_building_cluster
 */
class m190603_073125_add_colum_one_signal_app_id_in_building_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'one_signal_app_id', $this->string(255)->comment('mã app id gửi notify'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190603_073125_add_colum_one_signal_app_id_in_building_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190603_073125_add_colum_one_signal_app_id_in_building_cluster cannot be reverted.\n";

        return false;
    }
    */
}
