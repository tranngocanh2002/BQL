<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%resident_user_count_by_age}}`.
 */
class m200714_064135_create_resident_user_count_by_age_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%resident_user_count_by_age}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11),
            'start_age' => $this->integer(11),
            'end_age' => $this->integer(11),
            'total_foreigner' => $this->integer(11)->comment('Tổng người nước ngoài')->defaultValue(0),
            'total_vietnam' => $this->integer(11)->comment('Tổng người việt nam')->defaultValue(0),
            'total' => $this->integer(11)->defaultValue(0),
        ]);
        $this->createIndex( 'idx-resident_user_count_by_age-building_cluster_id','resident_user_count_by_age','building_cluster_id' );
        $this->createIndex( 'idx-resident_user_count_by_age-start_age','resident_user_count_by_age','start_age' );
        $this->createIndex( 'idx-resident_user_count_by_age-end_age','resident_user_count_by_age','end_age' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%resident_user_count_by_age}}');
    }
}
