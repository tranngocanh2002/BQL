<?php

use yii\db\Migration;

/**
 * Class m190918_021751_add_column_resident_user_id_in_gen_code
 */
class m190918_021751_add_column_resident_user_id_in_gen_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_gen_code', 'resident_user_id', $this->integer(11)->comment('Người tạo code với giao dịch chuyển khoản, chỉ người tạo mới hủy được'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190918_021751_add_column_resident_user_id_in_gen_code cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190918_021751_add_column_resident_user_id_in_gen_code cannot be reverted.\n";

        return false;
    }
    */
}
