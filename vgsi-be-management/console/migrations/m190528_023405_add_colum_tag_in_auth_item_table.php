<?php

use yii\db\Migration;

/**
 * Class m190528_023405_add_colum_tag_in_auth_item_table
 */
class m190528_023405_add_colum_tag_in_auth_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('auth_item', 'tag', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190528_023405_add_colum_tag_in_auth_item_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190528_023405_add_colum_tag_in_auth_item_table cannot be reverted.\n";

        return false;
    }
    */
}
