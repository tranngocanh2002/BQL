<?php

use yii\db\Migration;

/**
 * Class m200408_104031_add_column_limit_book_apartment
 */
class m200408_104031_add_column_limit_book_apartment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service_utility_config', 'limit_book_apartment', $this->integer(11)->defaultValue(10)->comment('Giới hạn lượt book của căn hộ / tháng'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200408_104031_add_column_limit_book_apartment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200408_104031_add_column_limit_book_apartment cannot be reverted.\n";

        return false;
    }
    */
}
