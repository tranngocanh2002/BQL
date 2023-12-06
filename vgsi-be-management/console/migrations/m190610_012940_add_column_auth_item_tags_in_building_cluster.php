<?php

use yii\db\Migration;

/**
 * Class m190610_012940_add_column_auth_item_tags_in_building_cluster
 */
class m190610_012940_add_column_auth_item_tags_in_building_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'auth_item_tags', $this->text());
        $this->alterColumn('building_cluster', 'hotline', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190610_012940_add_column_auth_item_tags_in_building_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190610_012940_add_column_auth_item_tags_in_building_cluster cannot be reverted.\n";

        return false;
    }
    */
}
