<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_utility_price}}`.
 */
class m200204_032723_create_service_utility_price_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('service_utility_config', 'start_time');
        $this->dropColumn('service_utility_config', 'end_time');
        $this->dropColumn('service_utility_config', 'price_hourly');
        $this->dropColumn('service_utility_config', 'price_adult');
        $this->dropColumn('service_utility_config', 'price_child');
        $this->createTable('{{%service_utility_price}}', [
            'id' => $this->primaryKey(),
            'service_utility_config_id' => $this->integer(11),
            'building_cluster_id' => $this->integer(11),
            'service_utility_free_id' => $this->integer(11),
            'start_time' => $this->integer(11),
            'end_time' => $this->integer(11),
            'price_hourly' => $this->integer(11)->comment('Giá theo 1 giờ'),
            'price_adult' => $this->integer(11)->comment('Giá 1 người lớn'),
            'price_child' => $this->integer(11)->comment('Giá 1 trẻ em'),
        ]);
        $this->createIndex( 'idx-service_utility_price-start_time','service_utility_price','start_time' );
        $this->createIndex( 'idx-service_utility_price-end_time','service_utility_price','end_time' );
        $this->createIndex( 'idx-service_utility_price-service_utility_config_id','service_utility_price','service_utility_config_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_utility_price}}');
    }
}
