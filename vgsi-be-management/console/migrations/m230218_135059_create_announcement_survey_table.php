<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%announcement_survey}}`.
 */
class m230218_135059_create_announcement_survey_table extends Migration
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
        $this->createTable('{{%announcement_survey}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'building_area_id' => $this->integer(11)->notNull(),
            'apartment_id' => $this->integer(11)->notNull(),
            'apartment_capacity' => $this->float()->defaultValue(0),
            'announcement_campaign_id' => $this->integer(11)->notNull(),
            'resident_user_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->defaultValue(0)->comment('0: chưa làm khảo sát, 1: đồng ý, 2: không đồng ý'),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-announcement_survey-building_cluster_id','announcement_survey','building_cluster_id' );
        $this->createIndex( 'idx-announcement_survey-announcement_campaign_id','announcement_survey','announcement_campaign_id' );
        $this->createIndex( 'idx-announcement_survey-resident_user_id','announcement_survey','resident_user_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%announcement_survey}}');
    }
}
