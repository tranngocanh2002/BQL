<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request_report_date}}`.
 */
class m190603_075947_create_request_report_date_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request_report_date}}', [
            'id' => $this->primaryKey(),
            'date' => $this->integer(11)->notNull()->comment('báo cáo ngày'),
            'status' => $this->integer(11)->comment('Trạng thái request'),
            'request_category_id' => $this->integer(11)->comment('Trạng thái request'),
            'total' => $this->integer(11)->comment('Tổng request'),
            'total_answer' => $this->integer(11)->comment('Tổng trả lời'),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%request_report_date}}');
    }
}
