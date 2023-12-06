<?php

use yii\db\Migration;

/**
 * Class m230503_023040_add_column_type_in_job_comment
 */
class m230503_023040_add_column_type_in_job_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('job_comment', 'type', $this->integer(11)->defaultValue(0)->comment('0 - comment, 1 - history'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230503_023040_add_column_type_in_job_comment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230503_023040_add_column_type_in_job_comment cannot be reverted.\n";

        return false;
    }
    */
}
