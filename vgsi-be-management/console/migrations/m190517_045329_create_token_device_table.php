<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%token_device}}`.
 */
class m190517_045329_create_token_device_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%notify_device_token}}');
        $this->createTable('{{%management_user_device_token}}', [
            'id' => $this->primaryKey(),
            'management_user_id' => $this->integer(11)->notNull()->comment('id user management'),
            'device_token' => $this->string(255)->comment('token nhận notify push theo từng device'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);
        $this->createIndex( 'idx-management_user_device_token-management_user_id','management_user_device_token','management_user_id' );

        $this->createTable('{{%resident_user_device_token}}', [
            'id' => $this->primaryKey(),
            'resident_user_id' => $this->integer(11)->notNull()->comment('id user resident'),
            'device_token' => $this->string(255)->comment('token nhận notify push theo từng device'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);
        $this->createIndex( 'idx-resident_user_device_token-resident_user_id','resident_user_device_token','resident_user_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%token_device}}');
    }
}
