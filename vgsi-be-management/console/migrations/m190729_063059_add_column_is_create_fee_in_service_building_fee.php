<?php

use yii\db\Migration;

/**
 * Class m190729_063059_add_column_is_create_fee_in_service_building_fee
 */
class m190729_063059_add_column_is_create_fee_in_service_building_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_building_fee', 'is_created_fee', $this->integer(11)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190729_063059_add_column_is_create_fee_in_service_building_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190729_063059_add_column_is_create_fee_in_service_building_fee cannot be reverted.\n";

        return false;
    }
    */
}
