<?php

use yii\db\Migration;

/**
 * Class m191114_022359_add_column_is_sync_in_resident_user_identification
 */
class m191114_022359_add_column_is_sync_in_resident_user_identification extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user_identification', 'is_sync', $this->integer(11)->defaultValue(0)->comment('0- chưa đồng bộ, 1 - đã đồng bộ'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191114_022359_add_column_is_sync_in_resident_user_identification cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191114_022359_add_column_is_sync_in_resident_user_identification cannot be reverted.\n";

        return false;
    }
    */
}
