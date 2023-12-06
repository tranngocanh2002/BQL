<?php

use yii\db\Migration;

/**
 * Class m190819_085440_add_column_type_in_service_debt
 */
class m190819_085440_add_column_type_in_service_debt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_debt', 'type', $this->integer(11)->defaultValue(0)->comment('0 - tháng cũ, 1 tháng hiện tại'));
        $this->createIndex( 'idx-service_debt-type','service_debt','type' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190819_085440_add_column_type_in_service_debt cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190819_085440_add_column_type_in_service_debt cannot be reverted.\n";

        return false;
    }
    */
}
