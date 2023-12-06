<?php

use yii\db\Migration;

/**
 * Class m190823_045319_add_column_type_default_in_category
 */
class m190823_045319_add_column_type_default_in_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_category', 'type', $this->integer(11)->comment('1 - default thông báo phí'));
        $this->addColumn('request_category', 'type', $this->integer(11)->comment('1 - default thông báo phí'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190823_045319_add_column_type_default_in_category cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190823_045319_add_column_type_default_in_category cannot be reverted.\n";

        return false;
    }
    */
}
