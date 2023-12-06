<?php

use yii\db\Migration;

/**
 * Class m230420_101513_add_column_reason_in_service_utility_form
 */
class m230420_101513_add_column_reason_in_service_utility_form extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_form', 'reason', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230420_101513_add_column_reason_in_service_utility_form cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230420_101513_add_column_reason_in_service_utility_form cannot be reverted.\n";

        return false;
    }
    */
}
