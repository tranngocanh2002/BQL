<?php

use yii\db\Migration;

/**
 * Class m230216_163051_add_column_in_announcement_campaign
 */
class m230216_163051_add_column_in_announcement_campaign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_campaign', 'is_survey', $this->integer(11)->defaultValue(0)->comment('1: thông báo khảo sát'));
        $this->addColumn('announcement_campaign', 'survey_deadline', $this->integer(11));
        $this->addColumn('announcement_campaign', 'targets', $this->string(255)->comment('mảng loại đối tượng nhận [0,1,2] theo type resident map apartment'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230216_163051_add_column_in_announcement_campaign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230216_163051_add_column_in_announcement_campaign cannot be reverted.\n";

        return false;
    }
    */
}
