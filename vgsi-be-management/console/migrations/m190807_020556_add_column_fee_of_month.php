<?php

use yii\db\Migration;

/**
 * Class m190807_020556_add_column_fee_of_month
 */
class m190807_020556_add_column_fee_of_month extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_parking_fee', 'fee_of_month', $this->integer(11));
        $this->addColumn('service_building_fee', 'fee_of_month', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190807_020556_add_column_fee_of_month cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190807_020556_add_column_fee_of_month cannot be reverted.\n";

        return false;
    }
    */
}
