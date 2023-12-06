<?php

use yii\db\Migration;

/**
 * Class m230608_162936_add_column_count_expire_in_job
 */
class m230608_162936_add_column_count_expire_in_job extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('job', 'count_expire', $this->integer(11)->defaultValue(9999));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230608_162936_add_column_count_expire_in_job cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230608_162936_add_column_count_expire_in_job cannot be reverted.\n";

        return false;
    }
    */
}
