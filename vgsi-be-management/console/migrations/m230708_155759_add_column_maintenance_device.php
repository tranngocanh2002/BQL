<?php

use yii\db\Migration;

/**
 * Class m230708_155759_add_column_maintenance_device
 */
class m230708_155759_add_column_maintenance_device extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('maintenance_device', 'maintenance_time_next', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230708_155759_add_column_maintenance_device cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230708_155759_add_column_maintenance_device cannot be reverted.\n";

        return false;
    }
    */
}
