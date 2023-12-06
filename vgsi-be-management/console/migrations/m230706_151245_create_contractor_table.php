<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%contractor}}`.
 */
class m230706_151245_create_contractor_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%contractor}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'address' => $this->string(255)->notNull(),
            'description' => $this->string(1000)->notNull(),
            'attach' => $this->text(),
            'contact_name' => $this->string(50)->notNull(),
            'contact_phone' => $this->string(11)->notNull(),
            'contact_email' => $this->string(50)->notNull(),
            'building_cluster_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->defaultValue(1)->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'created_by' => $this->integer(11),
            'updated_by' => $this->integer(11),
            'is_deleted' => $this->integer(11)->defaultValue(0),
        ], $tableOptions);
        $this->createIndex( 'idx-contractor-status','contractor','status' );
        $this->createIndex( 'idx-contractor-building_cluster_id','contractor','building_cluster_id' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%contractor}}');
    }
}
