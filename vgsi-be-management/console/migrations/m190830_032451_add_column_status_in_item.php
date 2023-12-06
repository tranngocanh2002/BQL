<?php

use yii\db\Migration;

/**
 * Class m190830_032451_add_column_status_in_item
 */
class m190830_032451_add_column_status_in_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_item', 'status_sms', $this->integer(11)->defaultValue(0)->comment('0- khởi tạo, 1 - thành công, 2 - thất bại'));
        $this->addColumn('announcement_item', 'status_email', $this->integer(11)->defaultValue(0)->comment('0- khởi tạo, 1 - thành công, 2 - thất bại'));
        $this->addColumn('announcement_item', 'status_notify', $this->integer(11)->defaultValue(0)->comment('0- khởi tạo, 1 - thành công, 2 - thất bại'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190830_032451_add_column_status_in_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190830_032451_add_column_status_in_item cannot be reverted.\n";

        return false;
    }
    */
}
