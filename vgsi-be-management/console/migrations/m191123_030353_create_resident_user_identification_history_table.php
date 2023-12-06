<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%resident_user_identification_history}}`.
 */
class m191123_030353_create_resident_user_identification_history_table extends Migration
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
        $this->createTable('{{%resident_user_identification_history}}', [
            'id' => $this->primaryKey(),
            'resident_user_id' => $this->integer(11),
            'type' => $this->integer(11)->comment('0 - là nhận diện cư dân, 1 - người lạ'),
            'time_event' => $this->integer(11),
            'image_name' => $this->string(255),
            'image_uri' => $this->string(255),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex( 'idx-resident_user_identification_history-resident_user_id','resident_user_identification_history','resident_user_id' );
        $this->createIndex( 'idx-resident_user_identification_history-type','resident_user_identification_history','type' );
        $this->createIndex( 'idx-resident_user_identification_history-time_event','resident_user_identification_history','time_event' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%resident_user_identification_history}}');
    }
}
