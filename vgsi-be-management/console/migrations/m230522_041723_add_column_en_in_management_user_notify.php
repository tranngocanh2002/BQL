<?php

use yii\db\Migration;

/**
 * Class m230522_041723_add_column_en_in_management_user_notify
 */
class m230522_041723_add_column_en_in_management_user_notify extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('management_user_notify', 'title_en', $this->string(255));
        $this->addColumn('management_user_notify', 'description_en', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230522_041723_add_column_en_in_management_user_notify cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230522_041723_add_column_en_in_management_user_notify cannot be reverted.\n";

        return false;
    }
    */
}
