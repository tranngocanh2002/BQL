<?php

use yii\db\Migration;

/**
 * Class m190819_074727_add_column_reminder_debt_in_apartment
 */
class m190819_074727_add_column_reminder_debt_in_apartment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment', 'reminder_debt', $this->integer(11)->defaultValue(0)->comment('Số lần thông báo nhắc nợ trong tháng'));
        $this->createIndex( 'idx-apartment-reminder_debt','apartment','reminder_debt' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190819_074727_add_column_reminder_debt_in_apartment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190819_074727_add_column_reminder_debt_in_apartment cannot be reverted.\n";

        return false;
    }
    */
}
