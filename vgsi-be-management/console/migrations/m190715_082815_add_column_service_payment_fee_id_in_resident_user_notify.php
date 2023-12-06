<?php

use yii\db\Migration;

/**
 * Class m190715_082815_add_column_service_payment_fee_id_in_resident_user_notify
 */
class m190715_082815_add_column_service_payment_fee_id_in_resident_user_notify extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user_notify', 'service_payment_fee_id', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190715_082815_add_column_service_payment_fee_id_in_resident_user_notify cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_082815_add_column_service_payment_fee_id_in_resident_user_notify cannot be reverted.\n";

        return false;
    }
    */
}
