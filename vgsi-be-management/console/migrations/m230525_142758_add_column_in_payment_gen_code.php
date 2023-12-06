<?php

use yii\db\Migration;

/**
 * Class m230525_142758_add_column_in_payment_gen_code
 */
class m230525_142758_add_column_in_payment_gen_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_gen_code', 'description', $this->text());
        $this->addColumn('payment_gen_code', 'image', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230525_142758_add_column_in_payment_gen_code cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230525_142758_add_column_in_payment_gen_code cannot be reverted.\n";

        return false;
    }
    */
}
