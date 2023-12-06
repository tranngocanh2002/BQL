<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post}}`.
 */
class m190611_024827_create_post_table extends Migration
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

        $this->createTable('{{%post_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'color' => $this->string(255)->comment('mã màu - nhãn màu hiển thị'),
            'building_cluster_id' => $this->integer(11),
            'building_area_id' => $this->integer(11),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 : chưa xóa, 1 : đã xóa'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-post_category-building_cluster_id','post_category','building_cluster_id' );
        $this->createIndex( 'idx-post_category-building_area_id','post_category','building_area_id' );
        $this->createIndex( 'idx-post_category-is_deleted','post_category','is_deleted' );

        $this->createTable('{{%post}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
            'content' => $this->text(),
            'medias' => $this->text(),
            'post_category_id' => $this->integer(11),
            'building_cluster_id' => $this->integer(11),
            'building_area_id' => $this->integer(11),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 : chưa xóa, 1 : đã xóa'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-post-post_category_id','post','post_category_id' );
        $this->createIndex( 'idx-post-building_cluster_id','post','building_cluster_id' );
        $this->createIndex( 'idx-post-building_area_id','post','building_area_id' );
        $this->createIndex( 'idx-post-is_deleted','post','is_deleted' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%post_category}}');
        $this->dropTable('{{%post}}');
    }
}
