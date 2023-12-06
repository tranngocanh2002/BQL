<?php

use yii\db\Migration;

/**
 * Class m190523_051550_add_token_hash_in_user_token_table
 */
class m190523_051550_add_token_hash_in_user_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user_access_token', 'token_hash', $this->string(255)->comment('mã hash sử dụng để query cho nhanh, có index'));
        $this->createIndex( 'idx-resident_user_access_token-token_hash','resident_user_access_token','token_hash' );

        $this->addColumn('management_user_access_token', 'token_hash', $this->string(255)->comment('mã hash sử dụng để query cho nhanh, có index'));
        $this->createIndex( 'idx-management_user_access_token-token_hash','management_user_access_token','token_hash' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190523_051550_add_token_hash_in_user_token_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190523_051550_add_token_hash_in_user_token_table cannot be reverted.\n";

        return false;
    }
    */
}
