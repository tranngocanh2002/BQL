<?php

use yii\db\Migration;

/**
 * Class m230614_155104_add_column_last_active_apartment
 */
class m230614_155104_add_column_last_active_apartment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment_map_resident_user', 'last_active', $this->integer(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230614_155104_add_column_last_active_apartment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230614_155104_add_column_last_active_apartment cannot be reverted.\n";

        return false;
    }
    */
}
