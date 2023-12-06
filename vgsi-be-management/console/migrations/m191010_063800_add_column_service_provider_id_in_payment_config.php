<?php

use yii\db\Migration;

/**
 * Class m191010_063800_add_column_service_provider_id_in_payment_config
 */
class m191010_063800_add_column_service_provider_id_in_payment_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_config','service_provider_id', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191010_063800_add_column_service_provider_id_in_payment_config cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191010_063800_add_column_service_provider_id_in_payment_config cannot be reverted.\n";

        return false;
    }
    */
}
