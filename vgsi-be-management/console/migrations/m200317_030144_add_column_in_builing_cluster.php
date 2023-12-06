<?php

use yii\db\Migration;

/**
 * Class m200317_030144_add_column_in_builing_cluster
 */
class m200317_030144_add_column_in_builing_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'link_whether', $this->string(255));
        $this->addColumn('building_cluster', 'email_account_push', $this->string(255));
        $this->addColumn('building_cluster', 'email_password_push', $this->string(255));
        $this->addColumn('building_cluster', 'sms_account_push', $this->string(255));
        $this->addColumn('building_cluster', 'sms_password_push', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200317_030144_add_column_in_builing_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200317_030144_add_column_in_builing_cluster cannot be reverted.\n";

        return false;
    }
    */
}
