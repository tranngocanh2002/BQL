<?php

use yii\db\Migration;

/**
 * Class m191001_094410_add_column_cash_instruction_in_building_cluster
 */
class m191001_094410_add_column_cash_instruction_in_building_cluster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building_cluster', 'cash_instruction', $this->text()->comment('Hướng dẫn thanh toán chuyển khoản'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191001_094410_add_column_cash_instruction_in_building_cluster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191001_094410_add_column_cash_instruction_in_building_cluster cannot be reverted.\n";

        return false;
    }
    */
}
