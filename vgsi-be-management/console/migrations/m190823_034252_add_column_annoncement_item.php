<?php

use yii\db\Migration;

/**
 * Class m190823_034252_add_column_annoncement_item
 */
class m190823_034252_add_column_annoncement_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_item', 'resident_user_name', $this->string(255));
        $this->addColumn('announcement_item', 'phone', $this->string(255));
        $this->addColumn('announcement_item', 'email', $this->string(255));
        $this->addColumn('announcement_item', 'read_email_at', $this->integer(11));
        $this->addColumn('announcement_item', 'type', $this->integer(11));
        $this->addColumn('announcement_item', 'end_debt', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190823_034252_add_column_annoncement_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190823_034252_add_column_annoncement_item cannot be reverted.\n";

        return false;
    }
    */
}
