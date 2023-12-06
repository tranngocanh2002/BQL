<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request}}`.
 */
class m190516_080103_create_request_table extends Migration
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

        $this->createTable('{{%request_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'color' => $this->string(255)->comment('mã màu - nhãn màu hiển thị'),
            'auth_group_ids' => $this->string(255)->comment('các nhóm phân quyền xử lý loại yêu cầu này'),
            'building_cluster_id' => $this->integer(11),
            'building_area_id' => $this->integer(11),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 : chưa xóa, 1 : đã xóa'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-request_category-building_cluster_id','request_category','building_cluster_id' );
        $this->createIndex( 'idx-request_category-building_area_id','request_category','building_area_id' );
        $this->createIndex( 'idx-request_category-is_deleted','request_category','is_deleted' );


        $this->createTable('{{%request}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
            'content' => $this->text(),
            'attach' => $this->text(),
            'request_category_id' => $this->integer(11),
            'resident_user_id' => $this->integer(11)->comment('Resident user tạo yêu cầu'),
            'building_cluster_id' => $this->integer(11),
            'building_area_id' => $this->integer(11),
            'apartment_id' => $this->integer(11),
            'status' => $this->integer(1)->defaultValue(0)->comment('Trạng thái xử lý của yêu cầu: -1 - hủy yêu cầu,0- khởi tạo, 1 - đang xử lý, 2 - đã xử lý xong'),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 : chưa xóa, 1 : đã xóa'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-request-status','request','status' );
        $this->createIndex( 'idx-request-resident_user_id','request','resident_user_id' );
        $this->createIndex( 'idx-request-request_category_id','request','request_category_id' );
        $this->createIndex( 'idx-request-building_cluster_id','request','building_cluster_id' );
        $this->createIndex( 'idx-request-building_area_id','request','building_area_id' );
        $this->createIndex( 'idx-request-is_deleted','request','is_deleted' );


        $this->createTable('{{%request_map_auth_group}}', [
            'auth_group_id' => $this->integer(11),
            'request_id' => $this->integer(11),
        ], $tableOptions);

        $this->createTable('{{%request_answer}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer(11),
            'resident_user_id' => $this->integer(11),
            'management_user_id' => $this->integer(11),
            'content' => $this->text(),
            'attach' => $this->text(),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 : chưa xóa, 1 : đã xóa'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-request_answer-request_id','request_answer','request_id' );
        $this->createIndex( 'idx-request_answer-resident_user_id','request_answer','resident_user_id' );
        $this->createIndex( 'idx-request_answer-management_user_id','request_answer','management_user_id' );
        $this->createIndex( 'idx-request_answer-is_deleted','request_answer','is_deleted' );

        $this->createTable('{{%request_answer_internal}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer(11),
            'management_user_id' => $this->integer(11),
            'content' => $this->text(),
            'attach' => $this->text(),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 : chưa xóa, 1 : đã xóa'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-request_answer_internal-request_id','request_answer_internal','request_id' );
        $this->createIndex( 'idx-request_answer_internal-management_user_id','request_answer_internal','management_user_id' );
        $this->createIndex( 'idx-request_answer_internal-is_deleted','request_answer_internal','is_deleted' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%request_category}}');
        $this->dropTable('{{%request}}');
        $this->dropTable('{{%request_map_auth_group}}');
        $this->dropTable('{{%request_answer}}');
        $this->dropTable('{{%request_answer_internal}}');
    }
}
