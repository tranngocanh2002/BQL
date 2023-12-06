<?php

use yii\db\Migration;

/**
 * Class m200414_083749_add_column_in_service_utlity_free
 */
class m200414_083749_add_column_in_service_utlity_free extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_free', 'timeout_pay_request', $this->integer(11)->defaultValue(10)->comment('Thời gian chờ tạo yêu cầu thanh toán: tính theo phút'));
        $this->addColumn('service_utility_free', 'limit_book_apartment', $this->integer(11)->defaultValue(10)->comment('Giới hạn lượt book của căn hộ / tháng'));
        $this->addColumn('service_utility_free', 'timeout_cancel_book', $this->integer(11)->defaultValue(120)->comment('Thời gian tối thiểu để được hủy book trước thời gian sử dụng'));

        $this->dropColumn('service_utility_config','timeout_pay_request');
        $this->dropColumn('service_utility_config','limit_book_apartment');
        $this->dropColumn('service_utility_config','timeout_cancel_book');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200414_083749_add_column_in_service_utlity_free cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200414_083749_add_column_in_service_utlity_free cannot be reverted.\n";

        return false;
    }
    */
}
