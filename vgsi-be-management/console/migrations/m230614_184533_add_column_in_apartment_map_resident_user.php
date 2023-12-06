<?php

use yii\db\Migration;

/**
 * Class m230614_184533_add_column_in_apartment_map_resident_user
 */
class m230614_184533_add_column_in_apartment_map_resident_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment_map_resident_user', 'is_deleted', $this->integer(1)->defaultValue(0));
        $this->addColumn('apartment_map_resident_user', 'deleted_at', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230614_184533_add_column_in_apartment_map_resident_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230614_184533_add_column_in_apartment_map_resident_user cannot be reverted.\n";

        return false;
    }
    */
}
