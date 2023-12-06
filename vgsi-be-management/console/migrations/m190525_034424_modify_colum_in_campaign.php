<?php

use yii\db\Migration;

/**
 * Class m190525_034424_modify_colum_in_campaign
 */
class m190525_034424_modify_colum_in_campaign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('announcement_campaign', 'title', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190525_034424_modify_colum_in_campaign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190525_034424_modify_colum_in_campaign cannot be reverted.\n";

        return false;
    }
    */
}
