<?php

use yii\db\Migration;

/**
 * Class m230711_233638_add_column_resident_user_phone_in_announcement_campaign
 */
class m230711_233638_add_column_resident_user_phone_in_announcement_campaign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('announcement_campaign', 'resident_user_phones', $this->text()->comment('mảng loại đối tượng nhận [8497xxx,8498xxx,8493xxxx] theo resident phone'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230711_233638_add_column_resident_user_phone_in_announcement_campaign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230711_233638_add_column_resident_user_phone_in_announcement_campaign cannot be reverted.\n";

        return false;
    }
    */
}
