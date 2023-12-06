<?php

use yii\db\Migration;

/**
 * Class m190717_035834_add_column_is_created_fee_in_parking_fee
 */
class m190717_035834_add_column_is_created_fee_in_parking_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_parking_fee', 'is_created_fee', $this->integer(1)->defaultValue(0)->comment('0 - chưa tạo phí thanh toán, 1 - đã tạo phí thanh toán => không được sửa'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190717_035834_add_column_is_created_fee_in_parking_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190717_035834_add_column_is_created_fee_in_parking_fee cannot be reverted.\n";

        return false;
    }
    */
}
