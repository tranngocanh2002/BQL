<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_utility_rateting}}`.
 */
class m230405_165500_create_service_utility_rateting_table extends Migration
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
        $this->createTable('{{%service_utility_rateting}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11),
            'apartment_id' => $this->integer(11),
            'service_utility_free_id' => $this->integer(11),
            'resident_user_id' => $this->integer(11),
            'star' => $this->float(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-service_utility_rateting-resident_user_id','service_utility_rateting','resident_user_id' );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_utility_rateting}}');
    }
}
