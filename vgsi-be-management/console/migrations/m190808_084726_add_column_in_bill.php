<?php

use yii\db\Migration;

/**
 * Class m190808_084726_add_column_in_bill
 */
class m190808_084726_add_column_in_bill extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_bill', 'management_user_name', $this->string(255)->comment('người thu tiền'));
        $this->addColumn('service_bill', 'payment_date', $this->integer(11)->comment('ngày nộp tiền'));
        $this->addColumn('service_bill', 'execution_date', $this->integer(11)->comment('ngày thực hiện'));
        $this->addColumn('service_bill', 'description', $this->text()->comment('mô tả phiếu thu'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190808_084726_add_column_in_bill cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190808_084726_add_column_in_bill cannot be reverted.\n";

        return false;
    }
    */
}
