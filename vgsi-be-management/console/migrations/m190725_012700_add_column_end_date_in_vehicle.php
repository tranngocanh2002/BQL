<?php

use yii\db\Migration;

/**
 * Class m190725_012700_add_column_end_date_in_vehicle
 */
class m190725_012700_add_column_end_date_in_vehicle extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_management_vehicle', 'end_date', $this->integer(11)->comment('ngày hết hạn dịch vụ'));
        $this->addColumn('service_management_vehicle', 'status', $this->integer(1)->defaultValue(1)->comment('0 - khởi tạo, 1 - đang hoạt động, 2 - đã hủy'));
        $this->addColumn('service_management_vehicle', 'is_deleted', $this->integer(1)->defaultValue(0)->comment('0 - chưa xóa, 1 - đã xóa'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190725_012700_add_column_end_date_in_vehicle cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190725_012700_add_column_end_date_in_vehicle cannot be reverted.\n";

        return false;
    }
    */
}
