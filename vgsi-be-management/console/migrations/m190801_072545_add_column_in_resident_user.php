<?php

use yii\db\Migration;

/**
 * Class m190801_072545_add_column_in_resident_user
 */
class m190801_072545_add_column_in_resident_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resident_user', 'cmtnd', $this->string(255));
        $this->addColumn('resident_user', 'nationality', $this->string(255)->comment('Quốc tịch'));
        $this->addColumn('resident_user', 'work', $this->string(255)->comment('Công việc'));
        $this->addColumn('resident_user', 'so_thi_thuc', $this->string(255)->comment('số thị thực'));
        $this->addColumn('resident_user', 'ngay_het_han_thi_thuc', $this->integer(11)->comment('Ngày hết hạn thị thực'));
        $this->addColumn('resident_user', 'ngay_dang_ky_tam_chu', $this->integer(11)->comment('Ngày đăng ký tạm chú'));
        $this->addColumn('resident_user', 'ngay_dang_ky_nhap_khau', $this->integer(11)->comment('Ngày đăng ký nhập khẩu'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190801_072545_add_column_in_resident_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190801_072545_add_column_in_resident_user cannot be reverted.\n";

        return false;
    }
    */
}
