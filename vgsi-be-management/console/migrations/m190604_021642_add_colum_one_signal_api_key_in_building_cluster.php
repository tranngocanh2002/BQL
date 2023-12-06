<?php

use yii\db\Migration;

/**
 * Class m190604_021642_add_colum_one_signal_api_key_in_building_cluster
 */
class m190604_021642_add_colum_one_signal_api_key_in_building_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'one_signal_api_key', $this->string(255)->comment('mã api key gửi notify'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190604_021642_add_colum_one_signal_api_key_in_building_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190604_021642_add_colum_one_signal_api_key_in_building_cluster cannot be reverted.\n";

        return false;
    }
    */
}
