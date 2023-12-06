<?php

use yii\db\Migration;

/**
 * Class m190920_030452_add_column_color_in_service_map_management
 */
class m190920_030452_add_column_color_in_service_map_management extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_map_management', 'color', $this->string(255)->defaultValue('#3e82f7'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190920_030452_add_column_color_in_service_map_management cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190920_030452_add_column_color_in_service_map_management cannot be reverted.\n";

        return false;
    }
    */
}
