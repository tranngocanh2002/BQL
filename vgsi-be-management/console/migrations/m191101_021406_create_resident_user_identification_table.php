<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%resident_user_identification}}`.
 */
class m191101_021406_create_resident_user_identification_table extends Migration
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

        $this->createTable('{{%resident_user_identification}}', [
            'id' => $this->primaryKey(),
            'resident_user_id' => $this->integer(11)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->defaultValue(0)->comment('0 - chưa xác thực, 1 - đã xác thực'),
            'medias' => $this->text(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex( 'idx-resident_user_identification-resident_user_id','resident_user_identification','resident_user_id' );
        $this->createIndex( 'idx-resident_user_identification-building_cluster_id','resident_user_identification','building_cluster_id' );
        $this->createIndex( 'idx-resident_user_identification-status','resident_user_identification','status' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%resident_user_identification}}');
    }
}
