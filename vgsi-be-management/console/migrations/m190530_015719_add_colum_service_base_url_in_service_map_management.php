<?php

use yii\db\Migration;

/**
 * Class m190530_015719_add_colum_service_base_url_in_service_map_management
 */
class m190530_015719_add_colum_service_base_url_in_service_map_management extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_map_management', 'service_base_url', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190530_015719_add_colum_service_base_url_in_service_map_management cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190530_015719_add_colum_service_base_url_in_service_map_management cannot be reverted.\n";

        return false;
    }
    */
}
