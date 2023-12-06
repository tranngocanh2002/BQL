<?php

use yii\db\Migration;

/**
 * Class m200913_055247_add_column_apartment_ids_in_campaign
 */
class m200913_055247_add_column_apartment_ids_in_campaign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_campaign', 'apartment_ids', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200913_055247_add_column_apartment_ids_in_campaign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200913_055247_add_column_apartment_ids_in_campaign cannot be reverted.\n";

        return false;
    }
    */
}
