<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notify_send_config}}`.
 */
class m200425_082158_create_notify_send_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notify_send_config}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'type' => $this->integer(11)->defaultValue(0)->comment('0 - thông báo thông thường, 1 - thông báo sự kiện, 2 - thông báo tài chính/công nợ, 3 - thông báo yêu cầu/phản ánh, 4 - booking dịch vụ, 5 - công việc'),
            'send_email' => $this->integer(11)->defaultValue(1)->comment('0 - ko gửi, 1 - có gửi'),
            'send_sms' => $this->integer(11)->defaultValue(0)->comment('0 - ko gửi, 1 - có gửi'),
            'send_notify_app' => $this->integer(11)->defaultValue(1)->comment('0 - ko gửi, 1 - có gửi'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ]);

        $this->createIndex( 'idx-notify_send_config-building_cluster_id','notify_send_config','building_cluster_id');
        $this->createIndex( 'idx-notify_send_config-type','notify_send_config','type');
        $this->createIndex( 'idx-notify_send_config-send_email','notify_send_config','send_email');
        $this->createIndex( 'idx-notify_send_config-send_sms','notify_send_config','send_sms');
        $this->createIndex( 'idx-notify_send_config-send_notify_app','notify_send_config','send_notify_app');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%notify_send_config}}');
    }
}
