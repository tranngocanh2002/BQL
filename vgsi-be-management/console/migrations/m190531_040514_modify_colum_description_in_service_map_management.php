<?php

use yii\db\Migration;

/**
 * Class m190531_040514_modify_colum_description_in_service_map_management
 */
class m190531_040514_modify_colum_description_in_service_map_management extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('service_map_management', 'service_description', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190531_040514_modify_colum_description_in_service_map_management cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190531_040514_modify_colum_description_in_service_map_management cannot be reverted.\n";

        return false;
    }
    */
}
