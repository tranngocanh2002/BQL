<?php

use yii\db\Migration;

/**
 * Class m190514_033208_add_colum_code_in_verify_code
 */
class m190514_033208_add_colum_code_in_verify_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('verify_code', 'code', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190514_033208_add_colum_code_in_verify_code cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190514_033208_add_colum_code_in_verify_code cannot be reverted.\n";

        return false;
    }
    */
}
