<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%management_user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(64)->notNull(),
            'password' => $this->string(64)->notNull(),
            'email' => $this->string(64)->notNull(),
            'phone' => $this->string(20),
            'first_name' => $this->string(64),
            'last_name' => $this->string(64),
            'avatar' => $this->string(255),
            'auth_key' => $this->string(),
            'gender' => $this->integer(1)->defaultValue(0)->comment('giới tính : 0 - chưa xác định, 1 - nam, 2 - nữ'),
            'birthday' => $this->integer(11)->comment('ngày sinh'),
            'parent_id' => $this->integer(11),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('0 : chưa kích hoạt, 1 : đã kích hoạt, 2 : bị khóa'),
            'status_verify_phone' => $this->smallInteger()->comment('0 - chưa xác thực , 1 đã xác thực'),
            'status_verify_email' => $this->smallInteger()->comment('0 - chưa xác thực , 1 đã xác thực'),
            'auth_group_id' => $this->integer(11),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 : chưa xóa, 1 : đã xóa'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-management_user-username','management_user','username' );
        $this->createIndex( 'idx-management_user-status','management_user','status' );
        $this->createIndex( 'idx-management_user-status_verify_phone','management_user','status_verify_phone' );
        $this->createIndex( 'idx-management_user-status_verify_email','management_user','status_verify_email' );
        $this->createIndex( 'idx-management_user-is_deleted','management_user','is_deleted' );
        $this->createIndex( 'idx-management_user-parent_id','management_user','parent_id' );
        $this->createIndex( 'idx-management_user-auth_group_id','management_user','auth_group_id' );


        $this->createTable('{{%management_user_access_token}}', [
            'id' => $this->primaryKey(),
            'management_user_id' => $this->integer(11),
            'token' => $this->text(),
            'type' => $this->integer(1)->defaultValue(0)->comment('0 - access token, 1 - refresh token'),
            'expired_at' => $this->integer(11)->comment('Thời gian hết hạn'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-management_user_access_token-management_user_id','management_user_access_token','management_user_id' );
        $this->createIndex( 'idx-management_user_access_token-type','management_user_access_token','type' );

        $this->createTable('{{%resident_user}}', [
            'id' => $this->primaryKey(),
            'phone' => $this->string(20)->notNull(),
            'password' => $this->string(64)->notNull(),
            'email' => $this->string(64),
            'first_name' => $this->string(64),
            'last_name' => $this->string(64),
            'avatar' => $this->string(255),
            'auth_key' => $this->string(),
            'notify_tags' => $this->string(255)->comment('Tags gửi notify'),
            'gender' => $this->integer(1)->defaultValue(0)->comment('giới tính : 0 - chưa xác định, 1 - nam, 2 - nữ'),
            'birthday' => $this->integer(11)->comment('ngày sinh'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('0 : chưa kích hoạt, 1 : đã kích hoạt, 2 : bị khóa'),
            'status_verify_phone' => $this->smallInteger()->comment('0 - chưa xác thực , 1 đã xác thực'),
            'status_verify_email' => $this->smallInteger()->comment('0 - chưa xác thực , 1 đã xác thực'),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 : chưa xóa, 1 : đã xóa'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-resident_user-phone','resident_user','phone' );
        $this->createIndex( 'idx-resident_user-status','resident_user','status' );
        $this->createIndex( 'idx-resident_user-status_verify_phone','resident_user','status_verify_phone' );
        $this->createIndex( 'idx-resident_user-status_verify_email','resident_user','status_verify_email' );
        $this->createIndex( 'idx-resident_user-is_deleted','resident_user','is_deleted' );


        $this->createTable('{{%resident_user_access_token}}', [
            'id' => $this->primaryKey(),
            'resident_user_id' => $this->integer(11),
            'token' => $this->text(),
            'type' => $this->integer(1)->defaultValue(0)->comment('0 - access token, 1 - refresh token'),
            'expired_at' => $this->integer(11)->comment('Thời gian hết hạn'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-resident_user_access_token-resident_user_id','resident_user_access_token','resident_user_id' );
        $this->createIndex( 'idx-resident_user_access_token-type','resident_user_access_token','type' );


        $this->createTable('{{%notify_device_token}}', [
            'id' => $this->primaryKey(),
            'resident_user_id' => $this->integer(11),
            'device_toke' => $this->string(255),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-notify_device_token-resident_user_id','notify_device_token','resident_user_id' );
        $this->createIndex( 'idx-notify_device_token-device_toke','notify_device_token','device_toke' );


        $this->createTable('{{%verify_code}}', [
            'id' => $this->primaryKey(),
            'status' => $this->integer(11)->defaultValue(0)->comment(' 0 - chưa verify , 1 - dã verify , 2 - bị hủy'),
            'type' => $this->integer(11)->defaultValue(0)->comment('cấc loại verify khác nhau'),
            'expired_at' => $this->integer(11)->notNull()->comment('Thời gian hết hạn'),
            'payload' => $this->text()->comment('các thông tin bổ xung'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-verify_code-status','verify_code','status' );
        $this->createIndex( 'idx-verify_code-type','verify_code','type' );
        $this->createIndex( 'idx-verify_code-expired_at','verify_code','expired_at' );


        $this->createTable('{{%file_upload}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64),
            'path' => $this->string(64),
            'type' => $this->string(64),
            'size' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createTable('{{%building_cluster}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull(),
            'domain' => $this->string(64)->notNull(),
            'email' => $this->string(64),
            'hotline' => $this->string(64),
            'address' => $this->string(255),
            'bank_account' => $this->string(255),
            'description' => $this->text(),
            'medias' => $this->text(),
            'status' => $this->integer(11)->defaultValue(0)->comment(' 0 - chưa active, 1 - đã active'),
            'tax_code' => $this->string(45),
            'tax_info' => $this->string(512),
            'city_id' => $this->integer(11)->comment('id thành phố'),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 : chưa xóa, 1 : đã xóa'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-building_cluster-status','building_cluster','status' );
        $this->createIndex( 'idx-building_cluster-city_id','building_cluster','city_id' );
        $this->createIndex( 'idx-building_cluster-is_deleted','building_cluster','is_deleted' );


        $this->createTable('{{%building_area}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull(),
            'description' => $this->text(),
            'medias' => $this->text(),
            'status' => $this->integer(11)->defaultValue(0)->comment(' 0 - chưa active, 1 - đã active'),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 : chưa xóa, 1 : đã xóa'),
            'building_cluster_id' => $this->integer(11),
            'parent_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-building_area-status','building_area','status' );
        $this->createIndex( 'idx-building_area-building_cluster_id','building_area','building_cluster_id' );
        $this->createIndex( 'idx-building_area-is_deleted','building_area','is_deleted' );
        $this->createIndex( 'idx-building_area-parent_id','building_area','parent_id' );


        $this->createTable('{{%apartment}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull(),
            'description' => $this->text(),
            'medias' => $this->text(),
            'status' => $this->integer(11)->defaultValue(0)->comment(' 0 - chưa active, 1 - đã active'),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 : chưa xóa, 1 : đã xóa'),
            'building_cluster_id' => $this->integer(11),
            'building_area_id' => $this->integer(11),
            'resident_user_id' => $this->integer(11)->comment('chủ hộ'),
            'capacity' => $this->integer(11)->defaultValue(0)->comment('diện tich : m2'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-apartment-status','apartment','status' );
        $this->createIndex( 'idx-apartment-building_cluster_id','apartment','building_cluster_id' );
        $this->createIndex( 'idx-apartment-is_deleted','apartment','is_deleted' );
        $this->createIndex( 'idx-apartment-building_area_id','apartment','building_area_id' );
        $this->createIndex( 'idx-apartment-resident_user_id','apartment','resident_user_id' );
        $this->createIndex( 'idx-apartment-capacity','apartment','capacity' );


        $this->createTable('{{%building_cluster_map_management_user}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11),
            'management_user_id' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-building_cluster_map_management_user-building_cluster_id','building_cluster_map_management_user','building_cluster_id' );
        $this->createIndex( 'idx-building_cluster_map_management_user-management_user_id','building_cluster_map_management_user','management_user_id' );

        $this->createTable('{{%apartment_map_resident_user}}', [
            'id' => $this->primaryKey(),
            'apartment_id' => $this->integer(11),
            'resident_user_id' => $this->integer(11),
            'building_cluster_id' => $this->integer(11),
            'building_area_id' => $this->integer(11),
            'type' => $this->integer(11)->defaultValue(0)->comment('0 - thành viên, 1 - chủ hộ, 2 - khách'),
            'status' => $this->integer(11)->defaultValue(1)->comment(' 0 - chưa active, 1 - đã active'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-apartment_map_resident_user-type','apartment_map_resident_user','type' );
        $this->createIndex( 'idx-apartment_map_resident_user-status','apartment_map_resident_user','status' );
        $this->createIndex( 'idx-apartment_map_resident_user-apartment_id','apartment_map_resident_user','apartment_id' );
        $this->createIndex( 'idx-apartment_map_resident_user-building_cluster_id','apartment_map_resident_user','building_cluster_id' );
        $this->createIndex( 'idx-apartment_map_resident_user-building_area_id','apartment_map_resident_user','building_area_id' );
        $this->createIndex( 'idx-apartment_map_resident_user-resident_user_id','apartment_map_resident_user','resident_user_id' );


        $this->createTable('{{%announcement_campaign}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(45),
            'description' => $this->string(255),
            'content' => $this->text(),
            'attach' => $this->text(),
            'status' => $this->integer(11)->defaultValue(1)->comment(' 0 - chưa active, 1 - đã active'),
            'is_send_push' => $this->integer(11),
            'is_send_email' => $this->integer(11),
            'is_send_sms' => $this->integer(11),
            'send_at' => $this->integer(11),
            'building_cluster_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-announcement_campaign-status','announcement_campaign','status' );
        $this->createIndex( 'idx-announcement_campaign-is_send_push','announcement_campaign','is_send_push' );
        $this->createIndex( 'idx-announcement_campaign-is_send_email','announcement_campaign','is_send_email' );
        $this->createIndex( 'idx-announcement_campaign-is_send_sms','announcement_campaign','is_send_sms' );
        $this->createIndex( 'idx-announcement_campaign-send_at','announcement_campaign','send_at' );
        $this->createIndex( 'idx-announcement_campaign-building_cluster_id','announcement_campaign','building_cluster_id' );


        $this->createTable('{{%announcement_item_send}}', [
            'id' => $this->primaryKey(),
            'announcement_campaign_id' => $this->integer(11),
            'building_cluster_id' => $this->integer(11),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11),
            'tags' => $this->string(255),
            'status' => $this->integer(11)->defaultValue(1)->comment(' 0 - đã gửi, 1 - thành công, 2 - thất bại'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-announcement_item_send-status','announcement_item_send','status' );
        $this->createIndex( 'idx-announcement_item_send-announcement_campaign_id','announcement_item_send','announcement_campaign_id' );
        $this->createIndex( 'idx-announcement_item_send-building_cluster_id','announcement_item_send','building_cluster_id' );
        $this->createIndex( 'idx-announcement_item_send-building_area_id','announcement_item_send','building_area_id' );
        $this->createIndex( 'idx-announcement_item_send-apartment_id','announcement_item_send','apartment_id' );


        $this->createTable('{{%announcement_item}}', [
            'id' => $this->primaryKey(),
            'announcement_campaign_id' => $this->integer(11),
            'building_cluster_id' => $this->integer(11),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11),
            'read_at' => $this->integer(11),
            'status' => $this->integer(11)->defaultValue(1)->comment('  0 - đã gửi, 1 - thành công, 2 - thất bại'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-announcement_item-read_at','announcement_item','read_at' );
        $this->createIndex( 'idx-announcement_item-status','announcement_item','status' );
        $this->createIndex( 'idx-announcement_item-announcement_campaign_id','announcement_item','announcement_campaign_id' );
        $this->createIndex( 'idx-announcement_item-building_cluster_id','announcement_item','building_cluster_id' );
        $this->createIndex( 'idx-announcement_item-building_area_id','announcement_item','building_area_id' );
        $this->createIndex( 'idx-announcement_item-apartment_id','announcement_item','apartment_id' );
    }
}
