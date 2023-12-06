<?php

use yii\db\Migration;

/**
 * Class m190730_023923_add_column_in_fee
 */
class m190730_023923_add_column_in_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_building_fee', 'json_desc', $this->text());
        $this->addColumn('service_parking_fee', 'json_desc', $this->text());
        $this->addColumn('service_water_fee', 'json_desc', $this->text());
        $this->addColumn('service_utility_free', 'json_desc', $this->text());
        $this->addColumn('service_payment_fee', 'json_desc', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190730_023923_add_column_in_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190730_023923_add_column_in_fee cannot be reverted.\n";

        return false;
    }
    */
}
