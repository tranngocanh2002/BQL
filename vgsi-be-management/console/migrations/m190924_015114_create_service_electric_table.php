<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_electric}}`.
 */
class m190924_015114_create_service_electric_table extends Migration
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

        $this->createTable('{{%service_electric_config}}', [
            'id' => $this->primaryKey(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'type' => $this->integer(1)->defaultValue(0)->comment('0 - thu phí theo căn hộ, 1 - thu phí theo đầu người/ căn hộ'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex( 'idx-service_electric_config-type','service_electric_config','type' );
        $this->createIndex( 'idx-service_electric_config-service_map_management_id','service_electric_config','service_map_management_id' );
        $this->createIndex( 'idx-service_electric_config-building_cluster_id','service_electric_config','building_cluster_id' );
        $this->createIndex( 'idx-service_electric_config-building_area_id','service_electric_config','building_area_id' );

        $this->createTable('{{%service_electric_fee}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11),
            'service_map_management_id' => $this->integer(11),
            'start_index' => $this->integer(11)->defaultValue(0)->comment('chỉ số đầu'),
            'end_index' => $this->integer(11)->defaultValue(0)->comment('chỉ số cuối'),
            'total_index' => $this->integer(11)->defaultValue(0)->comment('tổng chỉ số sử dụng'),
            'total_money' => $this->integer(11)->defaultValue(0)->comment('tổng tiền'),
            'lock_time' => $this->integer(11)->defaultValue(0)->comment('thời gian chốt'),
            'description' => $this->text(),
            'json_desc' => $this->text(),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa duyệt, 1 - đã duyệt'),
            'is_created_fee' => $this->integer(1)->defaultValue(0)->comment('0 - chưa tạo phí thanh toán, 1 - đã tạo phí thanh toán => không được sửa'),
            'fee_of_month' => $this->integer(11),
            'start_time' => $this->integer(11),
            'service_payment_fee_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex('idx-service_electric_fee-service_map_management_id', 'service_electric_fee', 'service_map_management_id');
        $this->createIndex('idx-service_electric_fee-lock_time', 'service_electric_fee', 'lock_time');
        $this->createIndex('idx-service_electric_fee-status', 'service_electric_fee', 'status');
        $this->createIndex('idx-service_electric_fee-building_cluster_id', 'service_electric_fee', 'building_cluster_id');
        $this->createIndex('idx-service_electric_fee-is_created_fee', 'service_electric_fee', 'is_created_fee');
        $this->createIndex('idx-service_electric_fee-service_payment_fee_id', 'service_electric_fee', 'service_payment_fee_id');
        $this->createIndex('idx-service_electric_fee-fee_of_month', 'service_electric_fee', 'fee_of_month');

        $this->createTable('{{%service_electric_info}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11)->notNull(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'start_date' => $this->integer(11),
            'end_date' => $this->integer(11),
            'end_index' => $this->integer(11),
            'tmp_end_date' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_electric_info-apartment_id','service_electric_info','apartment_id' );
        $this->createIndex( 'idx-service_electric_info-service_map_management_id','service_electric_info','service_map_management_id' );
        $this->createIndex( 'idx-service_electric_info-building_cluster_id','service_electric_info','building_cluster_id' );
        $this->createIndex( 'idx-service_electric_info-building_area_id','service_electric_info','building_area_id' );
        $this->createIndex( 'idx-service_electric_info-start_date','service_electric_info','start_date' );
        $this->createIndex( 'idx-service_electric_info-end_date','service_electric_info','end_date' );

        $this->createTable('{{%service_electric_level}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull(),
            'description' => $this->string(255),
            'from_level' => $this->integer(11)->notNull()->defaultValue(0),
            'to_level' => $this->integer(11)->notNull()->defaultValue(0),
            'price' => $this->integer(11)->notNull()->defaultValue(0),
            'service_id' => $this->integer(11)->notNull(),
            'service_map_management_id' => $this->integer(11)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_electric_level-from_level','service_electric_level','from_level' );
        $this->createIndex( 'idx-service_electric_level-to_level','service_electric_level','to_level' );
        $this->createIndex( 'idx-service_electric_level-price','service_electric_level','price' );
        $this->createIndex( 'idx-service_electric_level-service_id','service_electric_level','service_id' );
        $this->createIndex( 'idx-service_electric_level-service_map_management_id','service_electric_level','service_map_management_id' );
        $this->createIndex( 'idx-service_electric_level-building_cluster_id','service_electric_level','building_cluster_id' );
        $this->createIndex( 'idx-service_electric_level-building_area_id','service_electric_level','building_area_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_electric}}');
    }
}
