<?php

use yii\db\Migration;

/**
 * Class m190613_023039_add_column_is_draft_in_service_payment_fee
 */
class m190613_023039_add_column_is_draft_in_service_payment_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_payment_fee', 'is_draft', $this->integer(1)->defaultValue(1)->comment('0 - không phải nháp , 1 - là nháp'));
        $this->createIndex( 'idx-service_payment_fee-is_draft','service_payment_fee','is_draft' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190613_023039_add_column_is_draft_in_service_payment_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190613_023039_add_column_is_draft_in_service_payment_fee cannot be reverted.\n";

        return false;
    }
    */
}
