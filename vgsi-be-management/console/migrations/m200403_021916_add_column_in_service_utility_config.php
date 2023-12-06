<?php

use yii\db\Migration;

/**
 * Class m200403_021916_add_column_in_service_utility_config
 */
class m200403_021916_add_column_in_service_utility_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_config', 'timeout_pay_request', $this->integer(11)->defaultValue(10)->comment('Thời gian chờ tạo yêu cầu thanh toán: tính theo phút'));
        $this->addColumn('service_utility_config', 'timeout_cancel_book', $this->integer(11)->defaultValue(120)->comment('Thời gian tối thiểu để được hủy book trước thời gian sử dụng'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200403_021916_add_column_in_service_utility_config cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200403_021916_add_column_in_service_utility_config cannot be reverted.\n";

        return false;
    }
    */
}
