<?php

use yii\db\Migration;

/**
 * Class m190529_015450_add_colum_service_type_in_service_map_management
 */
class m190529_015450_add_colum_service_type_in_service_map_management extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_map_management', 'service_type', $this->integer(11));
        $this->createIndex( 'idx-service_map_management-service_type','service_map_management','service_type' );
        $this->dropColumn('service_map_management', 'service_provider_name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190529_015450_add_colum_service_type_in_service_map_management cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190529_015450_add_colum_service_type_in_service_map_management cannot be reverted.\n";

        return false;
    }
    */
}
