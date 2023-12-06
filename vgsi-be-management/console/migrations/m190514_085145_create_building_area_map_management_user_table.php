<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%building_area_map_management_user}}`.
 */
class m190514_085145_create_building_area_map_management_user_table extends Migration
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

        $this->createTable('{{%building_area_map_management_user}}', [
            'id' => $this->primaryKey(),
            'building_area_id' => $this->integer(11),
            'management_user_id' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-building_area_map_management_user-building_area_id','building_area_map_management_user','building_area_id' );
        $this->createIndex( 'idx-building_area_map_management_user-management_user_id','building_area_map_management_user','management_user_id' );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%building_area_map_management_user}}');
    }
}
