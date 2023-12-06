<?php

use yii\db\Migration;

/**
 * Class m190605_013601_update_index_reqpest_report_date
 */
class m190605_013601_update_index_reqpest_report_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex( 'idx-request_report_date-date','request_report_date','date' );
        $this->createIndex( 'idx-request_report_date-status','request_report_date','status' );
        $this->createIndex( 'idx-request_report_date-request_category_id','request_report_date','request_category_id' );
        $this->createIndex( 'idx-request_report_date-total','request_report_date','total' );
        $this->createIndex( 'idx-request_report_date-building_cluster_id','request_report_date','building_cluster_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190605_013601_update_index_reqpest_report_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190605_013601_update_index_reqpest_report_date cannot be reverted.\n";

        return false;
    }
    */
}
