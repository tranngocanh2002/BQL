<?php

use yii\db\Migration;

/**
 * Class m190603_014655_add_colum_icon_name_in_service
 */
class m190603_014655_add_colum_icon_name_in_service extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service', 'icon_name', $this->string(255));
        $this->addColumn('service_map_management', 'service_icon_name', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190603_014655_add_colum_icon_name_in_service cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190603_014655_add_colum_icon_name_in_service cannot be reverted.\n";

        return false;
    }
    */
}
