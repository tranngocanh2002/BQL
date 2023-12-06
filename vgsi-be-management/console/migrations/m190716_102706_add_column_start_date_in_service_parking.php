<?php

use yii\db\Migration;

/**
 * Class m190716_102706_add_column_start_date_in_service_parking
 */
class m190716_102706_add_column_start_date_in_service_parking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_management_vehicle', 'start_date', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190716_102706_add_column_start_date_in_service_parking cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190716_102706_add_column_start_date_in_service_parking cannot be reverted.\n";

        return false;
    }
    */
}
