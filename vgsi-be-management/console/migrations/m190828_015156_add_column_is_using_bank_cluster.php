<?php

use yii\db\Migration;

/**
 * Class m190828_015156_add_column_is_using_bank_cluster
 */
class m190828_015156_add_column_is_using_bank_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_provider', 'using_bank_cluster', $this->integer(11)->defaultValue(1)->comment('0 - sử dụng tài khoản riêng, sử dụng tài khoản của ban quan lý'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190828_015156_add_column_is_using_bank_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190828_015156_add_column_is_using_bank_cluster cannot be reverted.\n";

        return false;
    }
    */
}
