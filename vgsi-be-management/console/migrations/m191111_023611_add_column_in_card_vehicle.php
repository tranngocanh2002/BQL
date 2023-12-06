<?php

use yii\db\Migration;

/**
 * Class m191111_023611_add_column_in_card_vehicle
 */
class m191111_023611_add_column_in_card_vehicle extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_management_vehicle', 'type', $this->integer(11)->defaultValue(1)->comment('1 - xe máy, 2 - oto'));
        $this->addColumn('card_management', 'type', $this->integer(11)->defaultValue(1)->comment('1 - thẻ từ, 2 - thẻ rfid'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191111_023611_add_column_in_card_vehicle cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191111_023611_add_column_in_card_vehicle cannot be reverted.\n";

        return false;
    }
    */
}
