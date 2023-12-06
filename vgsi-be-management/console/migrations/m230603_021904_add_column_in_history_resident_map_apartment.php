<?php

use yii\db\Migration;

/**
 * Class m230603_021904_add_column_in_history_resident_map_apartment
 */
class m230603_021904_add_column_in_history_resident_map_apartment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('history_resident_map_apartment', 'resident_user_phone', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230603_021904_add_column_in_history_resident_map_apartment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230603_021904_add_column_in_history_resident_map_apartment cannot be reverted.\n";

        return false;
    }
    */
}
