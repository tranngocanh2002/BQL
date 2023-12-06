<?php

use yii\db\Migration;

/**
 * Class m190802_090758_add_column_in_resident
 */
class m190802_090758_add_column_in_resident extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user', 'ngay_cap_cmtnd', $this->integer(11));
        $this->addColumn('resident_user', 'noi_cap_cmtnd', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190802_090758_add_column_in_resident cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190802_090758_add_column_in_resident cannot be reverted.\n";

        return false;
    }
    */
}
