<?php

use yii\db\Migration;

/**
 * Class m190506_081425_add_all_table_rbac
 */
class m190506_081425_add_all_table_rbac extends Migration
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

        $auth_rule = 'CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
        $this->execute($auth_rule);

        $auth_item = 'CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `data_web` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
        $this->execute($auth_item);


        $auth_assignment = 'CREATE TABLE `auth_assignment`(
    `item_name` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
    `user_id` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
    `created_at` INT(11) DEFAULT NULL,
    PRIMARY KEY(`item_name`, `user_id`),
    KEY `idx-auth_assignment-user_id`(`user_id`),
    CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY(`item_name`) REFERENCES `auth_item`(`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci;';
        $this->execute($auth_assignment);


        $auth_item_child = 'CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
        $this->execute($auth_item_child);

        $this->createTable('{{%auth_item_web}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(64),
            'description' => $this->string(255),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-auth_item_web-code','auth_item_web','code' );

        $this->createTable('{{%auth_group}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(64),
            'name' => $this->string(64),
            'description' => $this->string(255),
            'data_role' => $this->text(),
            'building_cluster_id' => $this->integer(11),
            'building_area_id' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
        $this->createIndex( 'idx-auth_group-code','auth_group','code' );
        $this->createIndex( 'idx-auth_group-building_cluster_id','auth_group','building_cluster_id' );
        $this->createIndex( 'idx-auth_group-building_area_id','auth_group','building_area_id' );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190506_081425_add_all_table_rbac cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190506_081425_add_all_table_rbac cannot be reverted.\n";

        return false;
    }
    */
}
