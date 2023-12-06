<?php

use yii\db\Migration;

/**
 * Class m190617_084542_add_colum_order_in_post
 */
class m190617_084542_add_colum_order_in_post extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('post', 'order', $this->integer(11)->defaultValue(0));
        $this->addColumn('post_category', 'order', $this->integer(11)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190617_084542_add_colum_order_in_post cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190617_084542_add_colum_order_in_post cannot be reverted.\n";

        return false;
    }
    */
}
