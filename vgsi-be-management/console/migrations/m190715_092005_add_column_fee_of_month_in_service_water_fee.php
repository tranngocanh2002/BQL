<?php

use yii\db\Migration;

/**
 * Class m190715_092005_add_column_fee_of_month_in_service_water_fee
 */
class m190715_092005_add_column_fee_of_month_in_service_water_fee extends Migration
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
        echo "m190715_092005_add_column_fee_of_month_in_service_water_fee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_092005_add_column_fee_of_month_in_service_water_fee cannot be reverted.\n";

        return false;
    }
    */
}
