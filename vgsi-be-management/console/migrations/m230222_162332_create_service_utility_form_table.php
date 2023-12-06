<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_utility_form}}`.
 */
class m230222_162332_create_service_utility_form_table extends Migration
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
        $this->createTable('{{%service_utility_form}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'type' => $this->integer(11)->defaultValue(0)->comment('0: đăng ký sân chơi, 2: đăng ký thang máy, 3: ...'),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11)->notNull(),
            'apartment_id' => $this->integer(11)->notNull(),
            'resident_user_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->defaultValue(0)->comment('0: khởi tạo, 1: đồng ý, 2: không đồng ý'),
            'elements' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('Các thuộc tính trong form'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_utility_form-building_cluster_id','service_utility_form','building_cluster_id' );
        $this->createIndex( 'idx-service_utility_form-resident_user_id','service_utility_form','resident_user_id' );
        $this->createIndex( 'idx-service_utility_form-status','service_utility_form','status' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_utility_form}}');
    }
}
