<?php

use yii\db\Migration;

/**
 * Class m171219_114055_add_user_id_to_orders_table
 */
class m171219_114055_add_user_id_to_orders_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `orders` ADD COLUMN `user_id`  int(11) NULL COMMENT 'ID пользователя api bullosafe' AFTER `status`;");
        $this->execute("ALTER TABLE `orders` ADD CONSTRAINT `orders_ibfk_user_api` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `orders` DROP FOREIGN KEY `orders_ibfk_user_api`;");
        $this->execute("ALTER TABLE `orders` DROP COLUMN `user_id`");
    }
}
