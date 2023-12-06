<?php

use yii\db\Migration;

/**
 * Class m200217_074828_add_column_in_payment_config
 */
class m200217_074828_add_column_in_payment_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_config', 'partner_code', $this->string(255));
        $this->addColumn('payment_config', 'access_key', $this->string(255));
        $this->addColumn('payment_config', 'secret_key', $this->string(255));
        $this->addColumn('payment_config', 'merchant_name', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200217_074828_add_column_in_payment_config cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200217_074828_add_column_in_payment_config cannot be reverted.\n";

        return false;
    }
    */
}
