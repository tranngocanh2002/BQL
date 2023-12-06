<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%job}}`.
 */
class m230416_152430_create_job_table extends Migration
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
        $this->createTable('{{%job}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'performer' => $this->string(255)->comment('người xử lý'),
            'people_involved' => $this->string(255)->comment('người liên quan'),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - mới tạo, 1 - đang làm, 2 - làm xong'),
            'prioritize' => $this->integer(11)->defaultValue(0)->comment('0 - không ưu tiên, 1 - có ưu tiên'),
            'time_start' => $this->integer(11),
            'time_end' => $this->integer(11),
            'medias' => $this->text(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-job-status','job','status' );
        $this->createIndex( 'idx-job-building_cluster_id','job','building_cluster_id' );

        $this->createTable('{{%job_comment}}', [
            'id' => $this->primaryKey(),
            'content' => $this->text(),
            'medias' => $this->text(),
            'job_id' => $this->integer(11)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-job_comment-job_id','job_comment','job_id' );
        $this->createIndex( 'idx-job_comment-building_cluster_id','job_comment','building_cluster_id' );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%job}}');
    }
}
