<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service}}`.
 */
class m190528_025131_create_service_table extends Migration
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

        $this->createTable('{{%service}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->string(255),
            'logo' => $this->string(255),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa kích hoạt, 1 - đã kích hoạt'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service-status','service','status' );

        $this->createTable('{{%service_provider}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'address' => $this->string(255),
            'description' => $this->string(255),
            'medias' => $this->text(),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa kích hoạt, 1 - đã kích hoạt'),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 - chưa xóa, 1 - đã xóa'),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_provider-status','service_provider','status' );
        $this->createIndex( 'idx-service_provider-is_deleted','service_provider','is_deleted' );
        $this->createIndex( 'idx-service_provider-building_cluster_id','service_provider','building_cluster_id' );
        $this->createIndex( 'idx-service_provider-building_area_id','service_provider','building_area_id' );

        $this->createTable('{{%service_provider_billing_info}}', [
            'id' => $this->primaryKey(),
            'cash_instruction' => $this->string(500)->comment('Hướng dẫn thành toán tiền mặt'),
            'transfer_instruction' => $this->string(1000)->comment('Hướng dẫn thanh toán truyển khoản'),
            'bank_name' => $this->string(255)->comment('Tên ngân hàng'),
            'bank_number' => $this->string(255)->comment('Số tài khoản'),
            'bank_holders' => $this->string(255)->comment('Chủ tài khoản'),
            'service_provider_id' => $this->integer(11),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa kích hoạt, 1 - đã kích hoạt'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_provider_billing_info-status','service_provider_billing_info','status' );
        $this->createIndex( 'idx-service_provider_billing_info-service_provider_id','service_provider_billing_info','service_provider_id' );

        $this->createTable('{{%service_map_management}}', [
            'id' => $this->primaryKey(),
            'service_id' => $this->integer(11)->notNull(),
            'service_name' => $this->string(255),
            'service_description' => $this->string(255),
            'service_provider_id' => $this->integer(11)->notNull(),
            'service_provider_name' => $this->string(255),
            'medias' => $this->text(),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa kích hoạt, 1 - đã kích hoạt'),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 - chưa xóa, 1 - đã xóa'),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_map_management-status','service_map_management','status' );
        $this->createIndex( 'idx-service_map_management-is_deleted','service_map_management','is_deleted' );
        $this->createIndex( 'idx-service_map_management-building_cluster_id','service_map_management','building_cluster_id' );
        $this->createIndex( 'idx-service_map_management-building_area_id','service_map_management','building_area_id' );

        $this->createTable('{{%service_water}}', [
            'id' => $this->primaryKey(),
            'service_id' => $this->integer(11),
            'type' => $this->integer(11)->defaultValue(0)->comment('0 - dich vu he thong, 1 - dich vu phat sinh'),
            'type_target' => $this->integer(11)->defaultValue(0)->comment('0 - theo phòng, 1 theo resident'),
            'base_url' => $this->string(255)->comment('Link chuyển màn hình trên web'),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa kích hoạt, 1 - đã kích hoạt'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_water-status','service_water','status' );
        $this->createIndex( 'idx-service_water-service_id','service_water','service_id' );
        $this->createIndex( 'idx-service_water-type','service_water','type' );
        $this->createIndex( 'idx-service_water-type_target','service_water','type_target' );

        $this->createTable('{{%service_water_level}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull(),
            'description' => $this->string(255),
            'from_level' => $this->integer(11)->notNull()->defaultValue(0),
            'to_level' => $this->integer(11)->notNull()->defaultValue(0),
            'price' => $this->integer(11)->notNull()->defaultValue(0),
            'service_water_id' => $this->integer(11)->notNull(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_water_level-from_level','service_water_level','from_level' );
        $this->createIndex( 'idx-service_water_level-to_level','service_water_level','to_level' );
        $this->createIndex( 'idx-service_water_level-price','service_water_level','price' );
        $this->createIndex( 'idx-service_water_level-service_water_id','service_water_level','service_water_id' );
        $this->createIndex( 'idx-service_water_level-service_map_management_id','service_water_level','service_map_management_id' );
        $this->createIndex( 'idx-service_water_level-building_cluster_id','service_water_level','building_cluster_id' );
        $this->createIndex( 'idx-service_water_level-building_area_id','service_water_level','building_area_id' );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service}}');
        $this->dropTable('{{%service_provider}}');
        $this->dropTable('{{%service_provider_billing_info}}');
        $this->dropTable('{{%service_map_management}}');
        $this->dropTable('{{%service_water}}');
        $this->dropTable('{{%service_water_level}}');
    }
}
