<?php

use yii\db\Migration;

/**
 * Class m191022_022033_add_column_sms_price_in_building_cluster
 */
class m191022_022033_add_column_sms_price_in_building_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'sms_price', $this->integer(11)->defaultValue(0)->comment('Giá mỗi tin nhắn'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191022_022033_add_column_sms_price_in_building_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191022_022033_add_column_sms_price_in_building_cluster cannot be reverted.\n";

        return false;
    }
    */
}
