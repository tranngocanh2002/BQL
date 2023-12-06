<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%wards}}`.
 */
class m190515_012904_create_wards_table extends Migration
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
        $this->createTable('{{%wards}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull(),
            'city_id' => $this->integer(11)->notNull(),
            'district_id' => $this->integer(11)->notNull()
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%wards}}');
    }
}
