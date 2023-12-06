<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%history_resident_map_apartment}}`.
 */
class m200806_042923_create_history_resident_map_apartment_table extends Migration
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
        $this->createTable('{{%history_resident_map_apartment}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(),
            'apartment_id' => $this->integer(),
            'apartment_name' => $this->string(),
            'apartment_parent_path' => $this->string(),
            'resident_user_id' => $this->integer(),
            'type' => $this->integer(),
            'time_in' => $this->integer(),
            'time_out' => $this->integer(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%history_resident_map_apartment}}');
    }
}
