<?php

use yii\db\Migration;

/**
 * Class m200719_101624_modify_column_utf8mb4
 */
class m200719_101624_modify_column_utf8mb4 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('building_cluster', 'description', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('announcement_campaign', 'description', $this->string(255)->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('announcement_campaign', 'content', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('announcement_item', 'description', $this->string(255)->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('announcement_item', 'content', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('announcement_template', 'content_email', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('announcement_template', 'content_app', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('request', 'content', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('request_answer', 'content', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('request_answer_internal', 'content', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('service', 'description', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
        $this->alterColumn('service_map_management', 'service_description', $this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200719_101624_modify_column_utf8mb4 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200719_101624_modify_column_utf8mb4 cannot be reverted.\n";

        return false;
    }
    */
}
