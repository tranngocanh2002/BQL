<?php

use yii\db\Migration;

/**
 * Class m190516_031058_add_colum_apartment_name_in_apartment_map_resident_user
 */
class m190516_031058_add_colum_apartment_name_in_apartment_map_resident_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment_map_resident_user', 'apartment_name', $this->string(255));
        $this->addColumn('apartment_map_resident_user', 'apartment_capacity', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190516_031058_add_colum_apartment_name_in_apartment_map_resident_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190516_031058_add_colum_apartment_name_in_apartment_map_resident_user cannot be reverted.\n";

        return false;
    }
    */
}
