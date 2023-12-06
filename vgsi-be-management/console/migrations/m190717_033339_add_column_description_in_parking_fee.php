<?php

use yii\db\Migration;

/**
 * Class m190717_033339_add_column_description_in_parking_fee
 */
class m190717_033339_add_column_description_in_parking_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_parking_fee', 'description', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190717_033339_add_column_description_in_parking_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190717_033339_add_column_description_in_parking_fee cannot be reverted.\n";

        return false;
    }
    */
}
