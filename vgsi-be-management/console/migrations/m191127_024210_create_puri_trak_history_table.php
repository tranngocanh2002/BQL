<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%puri_trak_history}}`.
 */
class m191127_024210_create_puri_trak_history_table extends Migration
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
        $this->createTable('{{%puri_trak_history}}', [
            'id' => $this->primaryKey(),
            'puri_trak_id' => $this->integer(11),
            'aqi' => $this->float(),
            'h' => $this->float(),
            't' => $this->float(),
            'time' => $this->integer(11),
            'device_id' => $this->string(255),
            'name' => $this->string(255),
            'lat' => $this->float(),
            'long' => $this->float(),
            'hours' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex( 'idx-puri_trak_history-puri_trak_id','puri_trak_history','puri_trak_id' );
        $this->createIndex( 'idx-puri_trak_history-device_id','puri_trak_history','device_id' );
        $this->createIndex( 'idx-puri_trak_history-hours','puri_trak_history','hours' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%puri_trak_history}}');
    }
}
