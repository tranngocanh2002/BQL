<?php

use yii\db\Migration;

/**
 * Class m190830_073117_add_column_errors_in_item
 */
class m190830_073117_add_column_errors_in_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_item', 'errors_sms', $this->text()->comment('Thông tin gửi lỗi nếu có'));
        $this->addColumn('announcement_item', 'errors_email', $this->text()->comment('Thông tin gửi lỗi nếu có'));
        $this->addColumn('announcement_item', 'errors_notify', $this->text()->comment('Thông tin gửi lỗi nếu có'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190830_073117_add_column_errors_in_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190830_073117_add_column_errors_in_item cannot be reverted.\n";

        return false;
    }
    */
}
