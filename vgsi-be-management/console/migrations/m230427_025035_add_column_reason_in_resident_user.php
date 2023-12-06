<?php

use yii\db\Migration;

/**
 * Class m230427_025035_add_column_reason_in_resident_user
 */
class m230427_025035_add_column_reason_in_resident_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user', 'reason', $this->string(500));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230427_025035_add_column_reason_in_resident_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230427_025035_add_column_reason_in_resident_user cannot be reverted.\n";

        return false;
    }
    */
}
