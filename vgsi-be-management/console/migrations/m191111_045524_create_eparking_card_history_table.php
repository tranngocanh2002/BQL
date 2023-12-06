<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%eparking_card_history}}`.
 */
class m191111_045524_create_eparking_card_history_table extends Migration
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
        $this->createTable('{{%eparking_card_history}}', [
            'id' => $this->primaryKey(),
            'serial' => $this->string(255),
            'vehicle_type' => $this->integer(11),
            'card_type' => $this->integer(11),
            'ticket_type' => $this->integer(11),
            'datetime_in' => $this->integer(11),
            'plate_in' => $this->string(255),
            'image1_in' => $this->string(255),
            'image2_in' => $this->string(255),
            'datetime_out' => $this->integer(11),
            'plate_out' => $this->string(255),
            'image1_out' => $this->string(255),
            'image2_out' => $this->string(255),
            'status' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex( 'idx-eparking_card_history-serial','eparking_card_history','serial' );
        $this->createIndex( 'idx-eparking_card_history-vehicle_type','eparking_card_history','vehicle_type' );
        $this->createIndex( 'idx-eparking_card_history-card_type','eparking_card_history','card_type' );
        $this->createIndex( 'idx-eparking_card_history-ticket_type','eparking_card_history','ticket_type' );
        $this->createIndex( 'idx-eparking_card_history-status','eparking_card_history','status' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%eparking_card_history}}');
    }
}
