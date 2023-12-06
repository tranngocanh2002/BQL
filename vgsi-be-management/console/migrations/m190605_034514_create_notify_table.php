<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notify}}`.
 */
class m190605_034514_create_notify_table extends Migration
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

        $this->createTable('{{%management_user_notify}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'management_user_id' => $this->integer(11),
            'title' => $this->string(255),
            'description' => $this->text(),
            'type' => $this->integer(11)->defaultValue(0)->comment('0 - request, 1 - request answer, 2 - request answer internal, 3 - service bill'),
            'is_read' => $this->integer(11)->defaultValue(0)->comment('0 - chưa đọc, 1 - đã đọc'),
            'is_hidden' => $this->integer(11)->defaultValue(0)->comment('0 - chưa ẩn, 1 - đã ẩn'),
            'request_id' => $this->integer(11),
            'request_answer_id' => $this->integer(11),
            'request_answer_internal_id' => $this->integer(11),
            'service_bill_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11)
        ], $tableOptions);

        $this->createIndex( 'idx-management_user_notify-building_cluster_id','management_user_notify','building_cluster_id' );
        $this->createIndex( 'idx-management_user_notify-building_area_id','management_user_notify','building_area_id' );
        $this->createIndex( 'idx-management_user_notify-management_user_id','management_user_notify','management_user_id' );
        $this->createIndex( 'idx-management_user_notify-type','management_user_notify','type' );
        $this->createIndex( 'idx-management_user_notify-is_read','management_user_notify','is_read' );
        $this->createIndex( 'idx-management_user_notify-is_hidden','management_user_notify','is_hidden' );
        $this->createIndex( 'idx-management_user_notify-request_id','management_user_notify','request_id' );
        $this->createIndex( 'idx-management_user_notify-request_answer_id','management_user_notify','request_answer_id' );
        $this->createIndex( 'idx-management_user_notify-request_answer_internal_id','management_user_notify','request_answer_internal_id' );
        $this->createIndex( 'idx-management_user_notify-service_bill_id','management_user_notify','service_bill_id' );

        $this->createTable('{{%resident_user_notify}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11),
            'resident_user_id' => $this->integer(11),
            'title' => $this->string(255),
            'description' => $this->text(),
            'type' => $this->integer(11)->defaultValue(0)->comment('0 - request, 1 - request answer, 2 - request answer internal, 3 - service bill, 4 - announcement'),
            'is_read' => $this->integer(11)->defaultValue(0)->comment('0 - chưa đọc, 1 - đã đọc'),
            'is_hidden' => $this->integer(11)->defaultValue(0)->comment('0 - chưa ẩn, 1 - đã ẩn'),
            'request_id' => $this->integer(11),
            'request_answer_id' => $this->integer(11),
            'request_answer_internal_id' => $this->integer(11),
            'service_bill_id' => $this->integer(11),
            'announcement_item_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11)
        ], $tableOptions);

        $this->createIndex( 'idx-resident_user_notify-building_cluster_id','resident_user_notify','building_cluster_id' );
        $this->createIndex( 'idx-resident_user_notify-building_area_id','resident_user_notify','building_area_id' );
        $this->createIndex( 'idx-resident_user_notify-resident_user_id','resident_user_notify','resident_user_id' );
        $this->createIndex( 'idx-resident_user_notify-type','resident_user_notify','type' );
        $this->createIndex( 'idx-resident_user_notify-is_read','resident_user_notify','is_read' );
        $this->createIndex( 'idx-resident_user_notify-is_hidden','resident_user_notify','is_hidden' );
        $this->createIndex( 'idx-resident_user_notify-request_id','resident_user_notify','request_id' );
        $this->createIndex( 'idx-resident_user_notify-request_answer_id','resident_user_notify','request_answer_id' );
        $this->createIndex( 'idx-resident_user_notify-request_answer_internal_id','resident_user_notify','request_answer_internal_id' );
        $this->createIndex( 'idx-resident_user_notify-service_bill_id','resident_user_notify','service_bill_id' );
        $this->createIndex( 'idx-resident_user_notify-announcement_item_id','resident_user_notify','announcement_item_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%management_user_notify}}');
        $this->dropTable('{{%resident_user_notify}}');
    }
}
