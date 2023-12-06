<?php

use yii\db\Migration;

/**
 * Class m230609_150626_add_column_in_resident_user_notify
 */
class m230609_150626_add_column_in_resident_user_notify extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user_notify', 'title_en', $this->string(255));
        $this->addColumn('resident_user_notify', 'description_en', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230609_150626_add_column_in_resident_user_notify cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230609_150626_add_column_in_resident_user_notify cannot be reverted.\n";

        return false;
    }
    */
}
