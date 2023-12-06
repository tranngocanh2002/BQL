<?php

use yii\db\Migration;

/**
 * Class m190927_022237_add_column_color_in_service
 */
class m190927_022237_add_column_color_in_service extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service', 'color', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190927_022237_add_column_color_in_service cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190927_022237_add_column_color_in_service cannot be reverted.\n";

        return false;
    }
    */
}
