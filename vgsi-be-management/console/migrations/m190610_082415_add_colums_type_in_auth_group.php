<?php

use yii\db\Migration;

/**
 * Class m190610_082415_add_colums_type_in_auth_group
 */
class m190610_082415_add_colums_type_in_auth_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('auth_group', 'type', $this->integer(1)->defaultValue(0)->comment('Loại tổ chức : 0 - Ban quản lý, 1 - Ban quản trị'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190610_082415_add_colums_type_in_auth_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190610_082415_add_colums_type_in_auth_group cannot be reverted.\n";

        return false;
    }
    */
}
