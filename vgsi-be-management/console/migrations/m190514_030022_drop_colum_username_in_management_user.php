<?php

use yii\db\Migration;

/**
 * Class m190514_030022_drop_colum_username_in_management_user
 */
class m190514_030022_drop_colum_username_in_management_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('management_user', 'username');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190514_030022_drop_colum_username_in_management_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190514_030022_drop_colum_username_in_management_user cannot be reverted.\n";

        return false;
    }
    */
}
