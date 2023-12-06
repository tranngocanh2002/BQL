<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%management_user_login_log}}`.
 */
class m190514_013031_create_management_user_login_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%management_user_login_log}}', [
            'id' => $this->primaryKey(),
            'type' => $this->integer(1)->defaultValue(0)->comment('0 - từ api, 1- từ backend'),
            'time' => $this->integer(11)->comment('Thời điểm login'),
            'ip' => $this->string(20)->comment('địa chỉ ip login')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%management_user_login_log}}');
    }
}
