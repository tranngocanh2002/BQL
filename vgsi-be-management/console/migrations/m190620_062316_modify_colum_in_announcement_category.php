<?php

use yii\db\Migration;

/**
 * Class m190620_062316_modify_colum_in_announcement_category
 */
class m190620_062316_modify_colum_in_announcement_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `announcement_category` CHANGE `name` `name` VARCHAR(125) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
        $this->execute("ALTER TABLE `announcement_category` CHANGE `label_color` `label_color` VARCHAR(125) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190620_062316_modify_colum_in_announcement_category cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190620_062316_modify_colum_in_announcement_category cannot be reverted.\n";

        return false;
    }
    */
}
