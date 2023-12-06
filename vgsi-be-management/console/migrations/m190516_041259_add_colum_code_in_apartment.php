<?php

use yii\db\Migration;

/**
 * Class m190516_041259_add_colum_code_in_apartment
 */
class m190516_041259_add_colum_code_in_apartment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment', 'code', $this->string(100));
        $this->createIndex( 'idx-apartment-code','apartment','code' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190516_041259_add_colum_code_in_apartment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190516_041259_add_colum_code_in_apartment cannot be reverted.\n";

        return false;
    }
    */
}
