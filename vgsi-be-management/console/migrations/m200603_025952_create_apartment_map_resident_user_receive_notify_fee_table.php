<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%apartment_map_resident_user_receive_notify_fee}}`.
 */
class m200603_025952_create_apartment_map_resident_user_receive_notify_fee_table extends Migration
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

        $this->createTable('{{%apartment_map_resident_user_receive_notify_fee}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11),
            'apartment_id' => $this->integer(11),
            'resident_user_id' => $this->integer(11)->comment('Tài khoản nhận thông báo trên app'),
            'phone' => $this->string(255)->comment('Số điện thoại nhận thông báo'),
            'email' => $this->string(255)->comment('Email nhận thông báo'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-AMRURNF-apartment_id','apartment_map_resident_user_receive_notify_fee','apartment_id' );
        $this->createIndex( 'idx-AMRURNF-building_cluster_id','apartment_map_resident_user_receive_notify_fee','building_cluster_id' );
        $this->createIndex( 'idx-AMRURNF-resident_user_id','apartment_map_resident_user_receive_notify_fee','resident_user_id' );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apartment_map_resident_user_receive_notify_fee}}');
    }
}
