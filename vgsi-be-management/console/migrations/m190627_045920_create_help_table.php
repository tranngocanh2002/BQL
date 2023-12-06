<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%help}}`.
 */
class m190627_045920_create_help_table extends Migration
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

        $this->createTable('{{%help_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'color' => $this->string(255)->comment('mã màu - nhãn màu hiển thị'),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 : chưa xóa, 1 : đã xóa'),
            'order' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-help_category-is_deleted','help_category','is_deleted' );

        $this->createTable('{{%help}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
            'content' => $this->text(),
            'medias' => $this->text(),
            'help_category_id' => $this->integer(11),
            'is_deleted' => $this->integer(1)->defaultValue(0)->comment('0 : chưa xóa, 1 : đã xóa'),
            'order' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-help-help_category_id','help','help_category_id' );
        $this->createIndex( 'idx-help-is_deleted','help','is_deleted' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%help_category}}');
        $this->dropTable('{{%help}}');
    }
}
