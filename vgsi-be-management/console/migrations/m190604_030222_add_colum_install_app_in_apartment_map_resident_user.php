<?php

use yii\db\Migration;

/**
 * Class m190604_030222_add_colum_install_app_in_apartment_map_resident_user
 */
class m190604_030222_add_colum_install_app_in_apartment_map_resident_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment_map_resident_user', 'install_app', $this->integer(11)->defaultValue(0)->comment('0 - chưa cài app, 1 đã cài app'));
        $this->createIndex('idx-apartment_map_resident_user-install_app', 'apartment_map_resident_user', 'install_app');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190604_030222_add_colum_install_app_in_apartment_map_resident_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190604_030222_add_colum_install_app_in_apartment_map_resident_user cannot be reverted.\n";

        return false;
    }
    */
}
