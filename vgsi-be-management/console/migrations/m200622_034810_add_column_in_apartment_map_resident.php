<?php

use yii\db\Migration;

/**
 * Class m200622_034810_add_column_in_apartment_map_resident
 */
class m200622_034810_add_column_in_apartment_map_resident extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment_map_resident_user', 'resident_user_nationality', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200622_034810_add_column_in_apartment_map_resident cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200622_034810_add_column_in_apartment_map_resident cannot be reverted.\n";

        return false;
    }
    */
}
