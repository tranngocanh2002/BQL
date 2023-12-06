<?php

use yii\db\Migration;

/**
 * Class m230531_154445_add_column_in_apartment_map_resident_user
 */
class m230531_154445_add_column_in_apartment_map_resident_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('apartment_map_resident_user', 'cmtnd', $this->string(255));
        $this->addColumn('apartment_map_resident_user', 'noi_cap_cmtnd', $this->string(255));
        $this->addColumn('apartment_map_resident_user', 'ngay_cap_cmtnd', $this->integer(11));
        $this->addColumn('apartment_map_resident_user', 'work', $this->string(255));
        $this->addColumn('apartment_map_resident_user', 'so_thi_thuc', $this->string(255));
        $this->addColumn('apartment_map_resident_user', 'ngay_dang_ky_nhap_khau', $this->integer(11));
        $this->addColumn('apartment_map_resident_user', 'ngay_dang_ky_tam_chu', $this->integer(11));
        $this->addColumn('apartment_map_resident_user', 'ngay_het_han_thi_thuc', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230531_154445_add_column_in_apartment_map_resident_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230531_154445_add_column_in_apartment_map_resident_user cannot be reverted.\n";

        return false;
    }
    */
}
