<?php

use yii\db\Migration;

/**
 * Class m200616_041259_add_column_vat_in_fee_config
 */
class m200616_041259_add_column_vat_in_fee_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_building_config', 'is_vat', $this->integer(11)->defaultValue(0)->comment('0 - chưa bao gồm vat, 1 - đã bao gồm vat'));
        $this->addColumn('service_building_config', 'vat_percent', $this->double()->defaultValue(0)->comment('% vat sẽ tính nếu có'));
        $this->addColumn('service_building_config', 'tax_percent', $this->double()->defaultValue(0)->comment('% thuế'));
        $this->addColumn('service_building_config', 'environ_percent', $this->double()->defaultValue(0)->comment('% phí bảo vệ môi trường'));

        $this->addColumn('service_electric_config', 'is_vat', $this->integer(11)->defaultValue(0)->comment('0 - chưa bao gồm vat, 1 - đã bao gồm vat'));
        $this->addColumn('service_electric_config', 'vat_percent', $this->double()->defaultValue(0)->comment('% vat sẽ tính nếu có'));
        $this->addColumn('service_electric_config', 'tax_percent', $this->double()->defaultValue(0)->comment('% thuế'));
        $this->addColumn('service_electric_config', 'environ_percent', $this->double()->defaultValue(0)->comment('% phí bảo vệ môi trường'));

        $this->addColumn('service_vehicle_config', 'is_vat', $this->integer(11)->defaultValue(0)->comment('0 - chưa bao gồm vat, 1 - đã bao gồm vat'));
        $this->addColumn('service_vehicle_config', 'vat_percent', $this->double()->defaultValue(0)->comment('% vat sẽ tính nếu có'));
        $this->addColumn('service_vehicle_config', 'tax_percent', $this->double()->defaultValue(0)->comment('% thuế'));
        $this->addColumn('service_vehicle_config', 'environ_percent', $this->double()->defaultValue(0)->comment('% phí bảo vệ môi trường'));

        $this->addColumn('service_water_config', 'is_vat', $this->integer(11)->defaultValue(0)->comment('0 - chưa bao gồm vat, 1 - đã bao gồm vat'));
        $this->addColumn('service_water_config', 'vat_percent', $this->double()->defaultValue(0)->comment('% vat sẽ tính nếu có'));
        $this->addColumn('service_water_config', 'tax_percent', $this->double()->defaultValue(0)->comment('% thuế'));
        $this->addColumn('service_water_config', 'environ_percent', $this->double()->defaultValue(0)->comment('% phí bảo vệ môi trường'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200616_041259_add_column_vat_in_fee_config cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200616_041259_add_column_vat_in_fee_config cannot be reverted.\n";

        return false;
    }
    */
}
