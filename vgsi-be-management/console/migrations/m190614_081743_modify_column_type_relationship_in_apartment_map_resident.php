<?php

use yii\db\Migration;

/**
 * Class m190614_081743_modify_column_type_relationship_in_apartment_map_resident
 */
class m190614_081743_modify_column_type_relationship_in_apartment_map_resident extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('apartment_map_resident_user', 'type_relationship', $this->integer(11)->defaultValue(7));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190614_081743_modify_column_type_relationship_in_apartment_map_resident cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190614_081743_modify_column_type_relationship_in_apartment_map_resident cannot be reverted.\n";

        return false;
    }
    */
}
