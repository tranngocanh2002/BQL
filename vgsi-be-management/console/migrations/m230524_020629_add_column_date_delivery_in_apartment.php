<?php

use yii\db\Migration;

/**
 * Class m230524_020629_add_column_date_delivery_in_apartment
 */
class m230524_020629_add_column_date_delivery_in_apartment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment', 'date_delivery', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230524_020629_add_column_date_delivery_in_apartment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230524_020629_add_column_date_delivery_in_apartment cannot be reverted.\n";

        return false;
    }
    */
}
