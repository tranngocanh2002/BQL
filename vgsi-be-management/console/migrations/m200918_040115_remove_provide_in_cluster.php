<?php

use yii\db\Migration;

/**
 * Class m200918_040115_remove_provide_in_cluster
 */
class m200918_040115_remove_provide_in_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200918_040115_remove_provide_in_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200918_040115_remove_provide_in_cluster cannot be reverted.\n";

        return false;
    }
    */
}
