<?php

use yii\db\Migration;

/**
 * Class m190722_091744_add_column_total_members_in_apartment
 */
class m190722_091744_add_column_total_members_in_apartment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment', 'total_members', $this->integer(11)->defaultValue(1)->comment('số thành viên trong căn hộ'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190722_091744_add_column_total_members_in_apartment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190722_091744_add_column_total_members_in_apartment cannot be reverted.\n";

        return false;
    }
    */
}
