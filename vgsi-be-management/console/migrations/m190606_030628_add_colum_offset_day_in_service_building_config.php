<?php

use yii\db\Migration;

/**
 * Class m190606_030628_add_colum_offset_day_in_service_building_config
 */
class m190606_030628_add_colum_offset_day_in_service_building_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_building_config', 'offset_day', $this->integer(11)->defaultValue(0)->comment('thời hạn nộp phí => số ngày sau khi có fee'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190606_030628_add_colum_offset_day_in_service_building_config cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190606_030628_add_colum_offset_day_in_service_building_config cannot be reverted.\n";

        return false;
    }
    */
}
