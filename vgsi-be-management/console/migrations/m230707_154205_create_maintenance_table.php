<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%maintenance}}`.
 */
class m230707_154205_create_maintenance_table extends Migration
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
        $this->createTable('{{%maintenance_device}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'code' => $this->string(255)->notNull(),
            'position' => $this->string(255)->comment('Vị trí thiết bị'),
            'description' => $this->string(1000),
            'attach' => $this->text(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->defaultValue(1)->notNull(),
            'guarantee_time_start' => $this->integer(11)->comment('thời gian bắt đầu bảo hành'),
            'guarantee_time_end' => $this->integer(11)->comment('thời gian kết thúc bảo hành'),
            'maintenance_time_start' => $this->integer(11)->notNull()->comment('thời gian bắt đầu bảo trì'),
            'maintenance_time_last' => $this->integer(11)->comment('thời gian bảo trì gần nhất'),
            'type' => $this->integer(11)->notNull()->defaultValue(0),
            'cycle' => $this->integer(11)->notNull()->defaultValue(1)->comment('Chu ký bảo chỉ theo tháng'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
            'is_deleted' => $this->integer(11)->defaultValue(0),
        ], $tableOptions);
        $this->createIndex( 'idx-maintenance_device-status','maintenance_device','status' );
        $this->createIndex( 'idx-maintenance_device-type','maintenance_device','type' );
        $this->createIndex( 'idx-maintenance_device-building_cluster_id','maintenance_device','building_cluster_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%maintenance}}');
    }
}
