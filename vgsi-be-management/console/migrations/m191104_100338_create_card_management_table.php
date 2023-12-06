<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%card_management}}`.
 */
class m191104_100338_create_card_management_table extends Migration
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

        $this->createTable('{{%card_management}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'apartment_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa xác thực, 1 - đã xác thực, 2 - hủy : toàn bộ dịch vụ ăn theo bị hủy'),
            'number' => $this->string(255)->comment('Số thẻ'),
            'resident_user_id' => $this->integer(11)->notNull()->comment('Chủ thẻ'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex( 'idx-card_management-building_cluster_id','card_management','building_cluster_id' );
        $this->createIndex( 'idx-card_management-apartment_id','card_management','apartment_id' );
        $this->createIndex( 'idx-card_management-status','card_management','status' );
        $this->createIndex( 'idx-card_management-number','card_management','number' );
        $this->createIndex( 'idx-card_management-resident_user_id','card_management','resident_user_id' );

        $this->createTable('{{%card_management_map_service}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'card_management_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa xác thực, 1 - đã xác thực, 2 - hủy'),
            'type' => $this->integer(11)->defaultValue(0)->comment('0 - thẻ cư dân, 1- thẻ xe ...'),
            'service_management_id' => $this->integer(11)->notNull()->comment('0 - id cư dân, 1- id xe ...'),
            'expiry_time' => $this->integer(11)->comment('Hạn sử dụng'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex( 'idx-card_management_map_service-building_cluster_id','card_management_map_service','building_cluster_id' );
        $this->createIndex( 'idx-card_management_map_service-card_management_id','card_management_map_service','card_management_id' );
        $this->createIndex( 'idx-card_management_map_service-status','card_management_map_service','status' );
        $this->createIndex( 'idx-card_management_map_service-service_management_id','card_management_map_service','service_management_id' );
        $this->createIndex( 'idx-card_management_map_service-expiry_time','card_management_map_service','expiry_time' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%card_management}}');
    }
}
