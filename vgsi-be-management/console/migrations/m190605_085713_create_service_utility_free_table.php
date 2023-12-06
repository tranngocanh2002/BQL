<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_utility_free}}`.
 */
class m190605_085713_create_service_utility_free_table extends Migration
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

        $this->createTable('{{%service_utility_free}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'code' => $this->string(255)->notNull(),
            'hours_open' => $this->string(255)->comment('giờ mở cửa 08:20 ..'),
            'hours_close' => $this->string(255)->comment('giờ đóng cửa 23:20 ..'),
            'description' => $this->text(),
            'medias' => $this->text(),
            'service_id' => $this->integer(11)->notNull(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_utility_free-code','service_utility_free','code' );
        $this->createIndex( 'idx-service_utility_free-service_id','service_utility_free','service_id' );
        $this->createIndex( 'idx-service_utility_free-service_map_management_id','service_utility_free','service_map_management_id' );
        $this->createIndex( 'idx-service_utility_free-building_cluster_id','service_utility_free','building_cluster_id' );
        $this->createIndex( 'idx-service_utility_free-building_area_id','service_utility_free','building_area_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_utility_free}}');
    }
}
