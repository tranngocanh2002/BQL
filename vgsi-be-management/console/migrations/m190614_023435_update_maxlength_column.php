<?php

use yii\db\Migration;

/**
 * Class m190614_023435_update_maxlength_column
 */
class m190614_023435_update_maxlength_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('service_payment_fee', 'description', $this->text());
        $this->alterColumn('service_provider', 'description', $this->text());
        $this->alterColumn('service_provider_billing_info', 'cash_instruction', $this->text());
        $this->alterColumn('service_provider_billing_info', 'transfer_instruction', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190614_023435_update_maxlength_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190614_023435_update_maxlength_column cannot be reverted.\n";

        return false;
    }
    */
}
