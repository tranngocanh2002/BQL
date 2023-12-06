<?php

use yii\db\Migration;

/**
 * Class m200320_043334_add_column_template_in_building_cluster
 */
class m200320_043334_add_column_template_in_building_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'service_bill_template', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200320_043334_add_column_template_in_building_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200320_043334_add_column_template_in_building_cluster cannot be reverted.\n";

        return false;
    }
    */
}
