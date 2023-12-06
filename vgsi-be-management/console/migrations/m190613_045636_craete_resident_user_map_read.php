<?php

use yii\db\Migration;

/**
 * Class m190613_045636_craete_resident_user_map_read
 */
class m190613_045636_craete_resident_user_map_read extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%resident_user_map_read}}', [
            'id' => $this->primaryKey(),
            'building_cluster_id' => $this->integer(11),
            'building_area_id' => $this->integer(11),
            'type' => $this->integer(11)->defaultValue(0)->comment('0 - yêu cầu, 1 - bản tin, 2 - thanh toán'),
            'is_read' => $this->integer(11)->defaultValue(0)->comment('0 - chưa đọc, 1 - đã đọc'),
            'resident_user_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190613_045636_craete_resident_user_map_read cannot be reverted.\n";
        $this->dropTable('{{%resident_user_map_read}}');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190613_045636_craete_resident_user_map_read cannot be reverted.\n";

        return false;
    }
    */
}
