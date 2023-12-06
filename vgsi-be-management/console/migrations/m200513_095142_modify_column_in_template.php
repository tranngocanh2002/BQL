<?php

use yii\db\Migration;

/**
 * Class m200513_095142_modify_column_in_template
 */
class m200513_095142_modify_column_in_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('announcement_template', 'building_cluster_id', $this->integer(11)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200513_095142_modify_column_in_template cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200513_095142_modify_column_in_template cannot be reverted.\n";

        return false;
    }
    */
}
