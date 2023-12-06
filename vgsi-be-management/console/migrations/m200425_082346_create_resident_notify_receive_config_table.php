<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%resident_notify_receive_config}}`.
 */
class m200425_082346_create_resident_notify_receive_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%resident_notify_receive_config}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'resident_user_id' => $this->integer(11)->notNull(),
            'channel' => $this->integer(11)->defaultValue(0)->comment('0 - notify app, 1 - email, 2 - sms'),
            'type' => $this->integer(11)->defaultValue(0)->comment('0 - thông báo thông thường, 1 - thông báo sự kiện, 2 - thông báo tài chính/công nợ, 3 - thông báo yêu cầu/phản ánh, 4 - booking dịch vụ, 5 - công việc'),
            'action_create' => $this->integer(11)->defaultValue(0)->comment('tạo mới: 0- ko nhận, 1 - có nhận'),
            'action_update' => $this->integer(11)->defaultValue(0)->comment('cập nhật: 0- ko nhận, 1 - có nhận'),
            'action_cancel' => $this->integer(11)->defaultValue(0)->comment('hủy: 0- ko nhận, 1 - có nhận'),
            'action_delete' => $this->integer(11)->defaultValue(0)->comment('xóa: 0- ko nhận, 1 - có nhận'),
            'action_approved' => $this->integer(11)->defaultValue(0)->comment('phê duyệt: 0- ko nhận, 1 - có nhận'),
            'action_comment' => $this->integer(11)->defaultValue(0)->comment('bình luận: 0- ko nhận, 1 - có nhận'),
            'action_rate' => $this->integer(11)->defaultValue(0)->comment('đánh giá: 0- ko nhận, 1 - có nhận'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);
        $this->createIndex( 'idx-resident_notify_receive_config-building_cluster_id','resident_notify_receive_config','building_cluster_id' );
        $this->createIndex( 'idx-resident_notify_receive_config-resident_user_id','resident_notify_receive_config','resident_user_id' );
        $this->createIndex( 'idx-resident_notify_receive_config-channel','resident_notify_receive_config','channel' );
        $this->createIndex( 'idx-resident_notify_receive_config-type','resident_notify_receive_config','type' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%resident_notify_receive_config}}');
    }
}
