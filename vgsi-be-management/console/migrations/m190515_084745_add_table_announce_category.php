<?php

use yii\db\Migration;

/**
 * Class m190515_084745_add_table_annouce_category
 */
class m190515_084745_add_table_announce_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('announce_category', [
            'id' => $this->primaryKey(11),
            'name' => $this->string(125)->notNull(),
            'label_color' => $this->string(255),
            'building_cluster_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190515_084745_add_table_annouce_category cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190515_084745_add_table_annouce_category cannot be reverted.\n";

        return false;
    }
    */
}
