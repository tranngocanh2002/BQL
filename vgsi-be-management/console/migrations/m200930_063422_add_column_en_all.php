<?php

use yii\db\Migration;

/**
 * Class m200930_063422_add_column_en_all
 */
class m200930_063422_add_column_en_all extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//        $this->addColumn('announcement_campaign', 'title_en', $this->string(255));
//        $this->addColumn('announcement_category', 'name_en', $this->string(255));
//        $this->addColumn('announcement_item', 'title_en', $this->string(255));
//        $this->addColumn('announcement_template', 'name_en', $this->string(255));
//        $this->addColumn('help_category', 'name_en', $this->string(255));
//        $this->addColumn('help', 'title_en', $this->string(255));
//        $this->addColumn('post_category', 'name_en', $this->string(255));
//        $this->addColumn('post', 'title_en', $this->string(255));
//        $this->addColumn('request', 'title_en', $this->string(255));
//        $this->addColumn('request_category', 'name_en', $this->string(255));
//        $this->addColumn('service_map_management', 'service_name_en', $this->string(255));
//        $this->addColumn('service_parking_level', 'name_en', $this->string(255));
//        $this->addColumn('service_electric_level', 'name_en', $this->string(255));
//        $this->addColumn('service_water_level', 'name_en', $this->string(255));
//        $this->addColumn('service_electric_fee', 'description_en', $this->text());
//        $this->addColumn('service_old_debit_fee', 'description_en', $this->text());
//        $this->addColumn('service_parking_fee', 'description_en', $this->text());
//        $this->addColumn('service_payment_fee', 'description_en', $this->text());
//        $this->addColumn('service_water_fee', 'description_en', $this->text());
//        $this->addColumn('service_building_fee', 'description_en', $this->text());
//        $this->addColumn('service_utility_config', 'name_en', $this->string(255));
//        $this->addColumn('service_utility_config', 'address_en', $this->string(255));
//        $this->addColumn('service_utility_free', 'name_en', $this->string(255));
//        $this->addColumn('auth_group', 'name_en', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200930_063422_add_column_en_all cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200930_063422_add_column_en_all cannot be reverted.\n";

        return false;
    }
    */
}
