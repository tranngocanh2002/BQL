<?php

use yii\db\Migration;

/**
 * Class m191206_024547_add_column_security_mode_in_building_cluster
 */
class m191206_024547_add_column_security_mode_in_building_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'security_mode', $this->integer(1)->defaultValue(0)->comment('0 - không kích hoạt, 1 - có kích hoạt'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191206_024547_add_column_security_mode_in_building_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191206_024547_add_column_security_mode_in_building_cluster cannot be reverted.\n";

        return false;
    }
    */
}
