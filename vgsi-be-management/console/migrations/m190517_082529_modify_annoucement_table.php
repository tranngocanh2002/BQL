<?php

use yii\db\Migration;

/**
 * Class m190517_082529_modify_annoucement_table
 */
class m190517_082529_modify_annoucement_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('announce_category','announcement_category');
        $this->addColumn('announcement_campaign', 'announcement_category_id', $this->integer(11));
        $this->addColumn('announcement_campaign', 'is_send', $this->integer(1)->defaultValue(0)->comment('1 - đã gửi, 0 - chưa gửi'));
        $this->createIndex( 'idx-announcement_campaign-announcement_category_id','announcement_campaign','announcement_category_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190517_082529_modify_annoucement_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190517_082529_modify_annoucement_table cannot be reverted.\n";

        return false;
    }
    */
}
