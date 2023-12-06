<?php

use yii\db\Migration;

/**
 * Class m190516_042640_add_colum_resident_in_apartment_map_resident_user
 */
class m190516_042640_add_colum_resident_in_apartment_map_resident_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment_map_resident_user', 'apartment_code', $this->string(100));
        $this->addColumn('apartment_map_resident_user', 'resident_user_phone', $this->string(100));
        $this->addColumn('apartment_map_resident_user', 'resident_user_email', $this->string(255));
        $this->addColumn('apartment_map_resident_user', 'resident_user_first_name', $this->string(255));
        $this->addColumn('apartment_map_resident_user', 'resident_user_last_name', $this->string(255));
        $this->addColumn('apartment_map_resident_user', 'resident_user_avatar', $this->string(255));
        $this->addColumn('apartment_map_resident_user', 'resident_user_gender', $this->integer(1));
        $this->addColumn('apartment_map_resident_user', 'resident_user_birthday', $this->integer(11));
        $this->createIndex( 'idx-apartment_map_resident_user-apartment_code','apartment_map_resident_user','apartment_code' );
        $this->createIndex( 'idx-apartment_map_resident_user-resident_user_phone','apartment_map_resident_user','resident_user_phone' );
        $this->createIndex( 'idx-apartment_map_resident_user-resident_user_gender','apartment_map_resident_user','resident_user_gender' );
        $this->createIndex( 'idx-apartment_map_resident_user-resident_user_birthday','apartment_map_resident_user','resident_user_birthday' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190516_042640_add_colum_resident_in_apartment_map_resident_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190516_042640_add_colum_resident_in_apartment_map_resident_user cannot be reverted.\n";

        return false;
    }
    */
}
