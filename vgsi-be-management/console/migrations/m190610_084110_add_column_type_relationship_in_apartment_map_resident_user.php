<?php

use yii\db\Migration;

/**
 * Class m190610_084110_add_column_type_relationship_in_apartment_map_resident_user
 */
class m190610_084110_add_column_type_relationship_in_apartment_map_resident_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment_map_resident_user', 'type_relationship', $this->integer(11)
            ->defaultValue(0)
            ->comment('Quan hệ với chủ hộ: 0 - Chủ hộ, 1 - Ông/Bà, 2 - Bố/Mẹ, 3 - Vợ/Chồng, 4 - Con, 5 - Anh/chị/em, 6 - Bạn, 7 - Khác'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190610_084110_add_column_type_relationship_in_apartment_map_resident_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190610_084110_add_column_type_relationship_in_apartment_map_resident_user cannot be reverted.\n";

        return false;
    }
    */
}
