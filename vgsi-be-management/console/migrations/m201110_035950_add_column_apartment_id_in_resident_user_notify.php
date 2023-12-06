<?php

use yii\db\Migration;

/**
 * Class m201110_035950_add_column_apartment_id_in_resident_user_notify
 */
class m201110_035950_add_column_apartment_id_in_resident_user_notify extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user_notify', 'apartment_id', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201110_035950_add_column_apartment_id_in_resident_user_notify cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201110_035950_add_column_apartment_id_in_resident_user_notify cannot be reverted.\n";

        return false;
    }
    */
}
