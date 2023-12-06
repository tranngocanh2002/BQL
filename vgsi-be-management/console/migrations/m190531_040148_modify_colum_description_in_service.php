<?php

use yii\db\Migration;

/**
 * Class m190531_040148_modify_colum_description_in_service
 */
class m190531_040148_modify_colum_description_in_service extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('service', 'description', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190531_040148_modify_colum_description_in_service cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190531_040148_modify_colum_description_in_service cannot be reverted.\n";

        return false;
    }
    */
}
