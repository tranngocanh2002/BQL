<?php

use yii\db\Migration;

/**
 * Class m190809_031419_modify_column_in_payment_fee
 */
class m190809_031419_modify_column_in_payment_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('service_payment_fee', 'service_bill_id', 'service_bill_ids');
        $this->alterColumn('service_payment_fee', 'service_bill_ids', $this->string(255));
        $this->renameColumn('service_payment_fee', 'service_bill_code', 'service_bill_codes');
        $this->alterColumn('service_payment_fee', 'service_bill_codes', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190809_031419_modify_column_in_payment_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190809_031419_modify_column_in_payment_fee cannot be reverted.\n";

        return false;
    }
    */
}
