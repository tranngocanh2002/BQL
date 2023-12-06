<?php

use yii\db\Migration;

/**
 * Class m190806_032842_add_column_tmp_end_date_in_fee_info
 */
class m190806_032842_add_column_tmp_end_date_in_fee_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_building_info', 'tmp_end_date', $this->integer(11));
        $this->addColumn('service_management_vehicle', 'tmp_end_date', $this->integer(11));
        $this->addColumn('service_water_info', 'tmp_end_date', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190806_032842_add_column_tmp_end_date_in_fee_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190806_032842_add_column_tmp_end_date_in_fee_info cannot be reverted.\n";

        return false;
    }
    */
}
