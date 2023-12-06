<?php

use yii\db\Migration;

/**
 * Class m200429_021314_modify_column_text_teamplate
 */
class m200429_021314_modify_column_text_teamplate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('announcement_template','content_email', $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'));
        $this->alterColumn('announcement_template','content_pdf', $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'));
        $this->alterColumn('building_cluster','service_bill_template', $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'));
        $this->alterColumn('service_bill_template','style', $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'));
        $this->alterColumn('service_bill_template','content', $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'));
        $this->alterColumn('service_bill_template','sub_content', $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200429_021314_modify_column_text_teamplate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200429_021314_modify_column_text_teamplate cannot be reverted.\n";

        return false;
    }
    */
}
