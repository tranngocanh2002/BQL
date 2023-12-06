<?php

use yii\db\Migration;

/**
 * Class m200907_062556_add_column_message_default_in_building_cluster
 */
class m200907_062556_add_column_message_default_in_building_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'message_request_default', $this->string(1000));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200907_062556_add_column_message_default_in_building_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200907_062556_add_column_message_default_in_building_cluster cannot be reverted.\n";

        return false;
    }
    */
}
