<?php

use yii\db\Migration;

/**
 * Class m190826_042818_add_column_content_pdf_in_announcement_template
 */
class m190826_042818_add_column_content_pdf_in_announcement_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_template', 'content_pdf', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190826_042818_add_column_content_pdf_in_announcement_template cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190826_042818_add_column_content_pdf_in_announcement_template cannot be reverted.\n";

        return false;
    }
    */
}
