<?php

use yii\db\Migration;

/**
 * Class m200911_100548_add_column_search_name_resident
 */
class m200911_100548_add_column_search_name_resident extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user', 'name_search', $this->string(255));
        $this->addColumn('apartment', 'resident_name_search', $this->string(255));
        $this->addColumn('apartment_map_resident_user', 'resident_name_search', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200911_100548_add_column_search_name_resident cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200911_100548_add_column_search_name_resident cannot be reverted.\n";

        return false;
    }
    */
}
