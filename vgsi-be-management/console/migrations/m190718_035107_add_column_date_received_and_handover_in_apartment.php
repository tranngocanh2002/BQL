<?php

use yii\db\Migration;

/**
 * Class m190718_035107_add_column_date_received_and_handover_in_apartment
 */
class m190718_035107_add_column_date_received_and_handover_in_apartment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment', 'handover', $this->string(255)->comment('người bàn giao'));
        $this->addColumn('apartment', 'date_received', $this->integer(11)->comment('ngày nhận'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190718_035107_add_column_date_received_and_handover_in_apartment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190718_035107_add_column_date_received_and_handover_in_apartment cannot be reverted.\n";

        return false;
    }
    */
}
