<?php

use common\models\User;
use common\models\UserRole;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m190527_023715_user_backend
 */
class m190527_023715_user_backend extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_role}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'permission' => $this->text()->notNull(),
        ],$tableOptions);

        $role_array = [];
        $role_array[] = [
            1,
            'admin',
            '{"user":["index","view","create","reset-password","delete"],"help":["index","view","create","update","delete"],"auth-group":["index","view","create","update","delete","get-by-cluster"],"help-category":["index","view","create","update","delete"],"service":["index","view","create","update","delete"],"user-role":["index","view","create","update","delete"],"payment-config":["index","view","create","update","delete"],"announcement-template":["index","view","create","update","delete"],"upload":["tmp"],"building-cluster":["index","view","create","update","delete"],"management-user":["index","view","create","update","delete","reset-password","reset-password-by-email"],"auth-item":["index","view","create","update","delete"],"site":["demo","index","login","request-password-reset","logout","reset-password-emaik"]}'
        ];
        Yii::$app->db->createCommand()->batchInsert('user_role', [
            'id', 'name', 'permission'
        ], $role_array)->execute();

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string(255),
            'email' => $this->string()->notNull()->unique(),
            'phone' => $this->string(25),
            'role_id' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $user_array = [];
        $user_array[] = [
            1,
            'admin',
            'lucnn@luci.vn',
            '$2y$13$XwLVSIM.lmrgGxMGOCLrwuPdGH0a2z.TeXpTv9rl/kVfR4mBHZOdK',
            '',
            1,
            time(),
            time(),
        ];
        Yii::$app->db->createCommand()->batchInsert('user', [
            'id', 'username', 'email', 'password_hash', 'auth_key', 'role_id', 'created_at', 'updated_at'
        ], $user_array)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190527_023715_user_backend cannot be reverted.\n";
        $this->dropTable('{{%user_role}}');
        $this->dropTable('{{%user}}');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190527_023715_user_backend cannot be reverted.\n";

        return false;
    }
    */
}
