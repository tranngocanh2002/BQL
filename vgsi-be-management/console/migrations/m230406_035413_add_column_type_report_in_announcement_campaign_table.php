<?php

use yii\db\Migration;

/**
 * Class m230406_035413_add_column_type_report_in_announcement_campaign_table
 */
class m230406_035413_add_column_type_report_in_announcement_campaign_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_campaign', 'type_report', $this->integer(11)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230406_035413_add_column_type_report_in_announcement_campaign_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230406_035413_add_column_type_report_in_announcement_campaign_table cannot be reverted.\n";

        return false;
    }
    */
}
