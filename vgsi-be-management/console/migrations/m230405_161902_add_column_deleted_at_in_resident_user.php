<?php

use yii\db\Migration;

/**
 * Class m230405_161902_add_column_deleted_at_in_resident_user
 */
class m230405_161902_add_column_deleted_at_in_resident_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user', 'deleted_at', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230405_161902_add_column_deleted_at_in_resident_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230405_161902_add_column_deleted_at_in_resident_user cannot be reverted.\n";

        return false;
    }
    */
}
