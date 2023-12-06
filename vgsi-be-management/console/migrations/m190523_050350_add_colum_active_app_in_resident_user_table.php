<?php

use yii\db\Migration;

/**
 * Class m190523_050350_add_colum_active_app_in_resident_user_table
 */
class m190523_050350_add_colum_active_app_in_resident_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user', 'active_app', $this->integer(1)->defaultValue(0)->comment('0 - chưa sử dụng app, 1 đã sử dụng app'));
        $this->createIndex( 'idx-resident_user-active_app','resident_user','active_app' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190523_050350_add_colum_active_app_in_resident_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190523_050350_add_colum_active_app_in_resident_user_table cannot be reverted.\n";

        return false;
    }
    */
}
