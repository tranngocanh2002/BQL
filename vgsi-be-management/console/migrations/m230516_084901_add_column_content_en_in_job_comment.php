<?php

use yii\db\Migration;

/**
 * Class m230516_084901_add_column_content_en_in_job_comment
 */
class m230516_084901_add_column_content_en_in_job_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('job_comment', 'content_en', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230516_084901_add_column_content_en_in_job_comment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230516_084901_add_column_content_en_in_job_comment cannot be reverted.\n";

        return false;
    }
    */
}
