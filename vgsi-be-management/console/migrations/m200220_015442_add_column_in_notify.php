<?php

use yii\db\Migration;

/**
 * Class m200220_015442_add_column_in_notify
 */
class m200220_015442_add_column_in_notify extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('management_user_notify', 'service_booking_id', $this->integer(11));
        $this->addColumn('resident_user_notify', 'service_booking_id', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200220_015442_add_column_in_notify cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200220_015442_add_column_in_notify cannot be reverted.\n";

        return false;
    }
    */
}
