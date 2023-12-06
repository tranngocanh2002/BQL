<?php

use yii\db\Migration;

/**
 * Class m190823_022119_add_column_type_in_announcement_template
 */
class m190823_022119_add_column_type_in_announcement_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_template', 'type', $this->integer(11));
        $this->createIndex( 'idx-announcement_template-type','announcement_template','type' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190823_022119_add_column_type_in_announcement_template cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190823_022119_add_column_type_in_announcement_template cannot be reverted.\n";

        return false;
    }
    */
}
