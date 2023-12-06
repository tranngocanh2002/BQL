<?php

use yii\db\Migration;

/**
 * Class m190608_064904_add_column_cr_in_announcement_campaign
 */
class m190608_064904_add_column_cr_in_announcement_campaign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_campaign', 'cr_minutes' , $this->string(255)->defaultValue('*'));
        $this->addColumn('announcement_campaign', 'cr_hours' , $this->string(255)->defaultValue('*'));
        $this->addColumn('announcement_campaign', 'cr_days' , $this->string(255)->defaultValue('*'));
        $this->addColumn('announcement_campaign', 'cr_months' , $this->string(255)->defaultValue('*'));
        $this->addColumn('announcement_campaign', 'cr_days_of_week' , $this->string(255)->defaultValue('*'));

        $this->createIndex( 'idx-announcement_campaign-cr_minutes','announcement_campaign','cr_minutes' );
        $this->createIndex( 'idx-announcement_campaign-cr_hours','announcement_campaign','cr_hours' );
        $this->createIndex( 'idx-announcement_campaign-cr_days','announcement_campaign','cr_days' );
        $this->createIndex( 'idx-announcement_campaign-cr_months','announcement_campaign','cr_months' );
        $this->createIndex( 'idx-announcement_campaign-cr_days_of_week','announcement_campaign','cr_days_of_week' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190608_064904_add_column_cr_in_announcement_campaign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190608_064904_add_column_cr_in_announcement_campaign cannot be reverted.\n";

        return false;
    }
    */
}
