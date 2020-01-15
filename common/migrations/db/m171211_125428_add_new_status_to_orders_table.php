<?php

use yii\db\Migration;

/**
 * Class m171211_125428_add_new_status_to_orders_table
 */
class m171211_125428_add_new_status_to_orders_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("
ALTER TABLE `orders`
MODIFY COLUMN `api_id`  int(11) NULL AFTER `id`,
MODIFY COLUMN `status`  enum('new','payed','calc') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'new' AFTER `currency_id`;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("
ALTER TABLE `orders`
MODIFY COLUMN `api_id`  int(11) NOT NULL AFTER `id`,
MODIFY COLUMN `status`  enum('new','payed') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'new' AFTER `currency_id`;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }

}
