<?php

use yii\db\Migration;

/**
 * Class m200912_102457_add_column_fee_id_text_search_in_booking
 */
class m200912_102457_add_column_fee_id_text_search_in_booking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_booking', 'service_payment_fee_ids_text_search', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200912_102457_add_column_fee_id_text_search_in_booking cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200912_102457_add_column_fee_id_text_search_in_booking cannot be reverted.\n";

        return false;
    }
    */
}
