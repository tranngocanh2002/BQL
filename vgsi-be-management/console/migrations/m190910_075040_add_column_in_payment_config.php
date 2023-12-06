<?php

use yii\db\Migration;

/**
 * Class m190910_075040_add_column_in_payment_config
 */
class m190910_075040_add_column_in_payment_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_config', 'return_web_url', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190910_075040_add_column_in_payment_config cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190910_075040_add_column_in_payment_config cannot be reverted.\n";

        return false;
    }
    */
}
