<?php

use yii\db\Migration;

/**
 * Class m190704_094428_add_column_setting_group_receives_notices_financial
 */
class m190704_094428_add_column_setting_group_receives_notices_financial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'setting_group_receives_notices_financial', $this->string(1000)->comment('nhóm quyền nhận thông báo về tài chính'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190704_094428_add_column_setting_group_receives_notices_financial cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190704_094428_add_column_setting_group_receives_notices_financial cannot be reverted.\n";

        return false;
    }
    */
}
