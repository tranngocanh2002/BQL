<?php

use yii\db\Migration;

/**
 * Class m200519_103440_add_column_in_template
 */
class m200519_103440_add_column_in_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_template', 'name', $this->string(255));
        $this->addColumn('announcement_template', 'image', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200519_103440_add_column_in_template cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200519_103440_add_column_in_template cannot be reverted.\n";

        return false;
    }
    */
}
