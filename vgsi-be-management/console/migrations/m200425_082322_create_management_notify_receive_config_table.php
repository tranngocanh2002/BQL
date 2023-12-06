<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%management_notify_receive_config}}`.
 */
class m200425_082322_create_management_notify_receive_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%management_notify_receive_config}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'management_user_id' => $this->integer(11)->notNull(),
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
        $this->createIndex( 'idx-management_notify_receive_config-building_cluster_id','management_notify_receive_config','building_cluster_id' );
        $this->createIndex( 'idx-management_notify_receive_config-management_user_id','management_notify_receive_config','management_user_id' );
        $this->createIndex( 'idx-management_notify_receive_config-channel','management_notify_receive_config','channel' );
        $this->createIndex( 'idx-management_notify_receive_config-type','management_notify_receive_config','type' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%management_notify_receive_config}}');
    }
}
