<?php

use yii\db\Migration;

/**
 * Class m190524_072243_add_colum_access_token_id_in_device_token
 */
class m190524_072243_add_colum_access_token_id_in_device_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('management_user_device_token', 'management_user_access_token_id', $this->integer(11));
        $this->createIndex( 'idx-management_user_device_token-management_user_access_token_id','management_user_device_token','management_user_access_token_id' );

        $this->addColumn('resident_user_device_token', 'resident_user_access_token_id', $this->integer(11));
        $this->createIndex( 'idx-resident_user_device_token-resident_user_access_token_id','resident_user_device_token','resident_user_access_token_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190524_072243_add_colum_access_token_id_in_device_token cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190524_072243_add_colum_access_token_id_in_device_token cannot be reverted.\n";

        return false;
    }
    */
}
