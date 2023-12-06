<?php

use yii\db\Migration;

/**
 * Class m200210_093844_add_column_book_time_in_service_utility_booking
 */
class m200210_093844_add_column_book_time_in_service_utility_booking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_booking','book_time', $this->text()->comment('Array Các khoảng thời gian book trong ngày'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200210_093844_add_column_book_time_in_service_utility_booking cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200210_093844_add_column_book_time_in_service_utility_booking cannot be reverted.\n";

        return false;
    }
    */
}
