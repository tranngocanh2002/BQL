<?php

use yii\db\Migration;

/**
 * Class m200329_181710_add_column_percent_in_config_fee
 */
class m200329_181710_add_column_percent_in_config_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_building_config', 'percent', $this->double()->defaultValue(0)->comment('% tính chênh lệch'));
        $this->addColumn('service_electric_config', 'percent', $this->double()->defaultValue(0)->comment('% tính chênh lệch'));
        $this->addColumn('service_vehicle_config', 'percent', $this->double()->defaultValue(0)->comment('% tính chênh lệch'));
        $this->addColumn('service_water_config', 'percent', $this->double()->defaultValue(0)->comment('% tính chênh lệch'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200329_181710_add_column_percent_in_config_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200329_181710_add_column_percent_in_config_fee cannot be reverted.\n";

        return false;
    }
    */
}
