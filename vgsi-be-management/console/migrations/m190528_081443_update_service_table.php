<?php

use yii\db\Migration;

/**
 * Class m190528_081443_update_service_table
 */
class m190528_081443_update_service_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%service_water}}');
        $this->dropColumn('{{%service_water_level}}', 'service_water_id');
        $this->addColumn('{{%service_water_level}}', 'service_id', $this->integer(11)->notNull());
        $this->createIndex( 'idx-service_water_level-service_id','service_water_level','service_id' );

        $this->addColumn('{{%service}}', 'service_type', $this->integer(11)->defaultValue(0)->comment('0 - Điện, 1 - Nước, 2 - Dịch vụ , ...'));
        $this->addColumn('{{%service}}', 'type', $this->integer(11)->defaultValue(0)->comment('0 - dich vu he thong, 1 - dich vu phat sinh'));
        $this->addColumn('{{%service}}', 'type_target', $this->integer(11)->defaultValue(0)->comment('0 - theo phòng, 1 theo resident'));
        $this->addColumn('{{%service}}', 'base_url', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190528_081443_update_service_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190528_081443_update_service_table cannot be reverted.\n";

        return false;
    }
    */
}
