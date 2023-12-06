<?php

use yii\db\Migration;

/**
 * Class m190530_015353_add_colum_parent_path_in_apartment
 */
class m190530_015353_add_colum_parent_path_in_apartment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment', 'parent_path', $this->string(255));
        $this->addColumn('apartment_map_resident_user', 'apartment_parent_path', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190530_015353_add_colum_parent_path_in_apartment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190530_015353_add_colum_parent_path_in_apartment cannot be reverted.\n";

        return false;
    }
    */
}
