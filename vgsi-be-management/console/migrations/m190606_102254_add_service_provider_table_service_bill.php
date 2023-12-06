<?php

use yii\db\Migration;

/**
 * Class m190606_102254_add_service_provider_table_service_bill
 */
class m190606_102254_add_service_provider_table_service_bill extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_bill', 'service_provider_id', 'int(11) default null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190606_102254_add_service_provider_table_service_bill cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190606_102254_add_service_provider_table_service_bill cannot be reverted.\n";

        return false;
    }
    */
}
