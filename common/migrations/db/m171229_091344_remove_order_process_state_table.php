<?php

use yii\db\Migration;

/**
 * Class m171229_091344_remove_order_process_state_table
 */
class m171229_091344_remove_order_process_state_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("DROP TABLE `order_process_state`;");
        $this->execute("
ALTER TABLE `orders`
MODIFY COLUMN `price`  decimal(10,2) NULL AFTER `api_id`,
MODIFY COLUMN `currency_id`  int(11) NULL AFTER `price`,
MODIFY COLUMN `program`  longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `calc_form`,
ADD COLUMN `slug`  varchar(64) NULL AFTER `program`,
ADD INDEX `slug_idx` (`slug`) ;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute("CREATE TABLE `order_process_state` (
`slug`  char(64) NOT NULL ,
`data`  mediumtext NOT NULL ,
`order_id`  int(11) NULL ,
`created_at`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`slug`),
FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);");
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("
ALTER TABLE `orders`
DROP COLUMN `slug`,
MODIFY COLUMN `price`  decimal(10,2) NOT NULL AFTER `api_id`,
MODIFY COLUMN `currency_id`  int(11) NOT NULL AFTER `price`,
MODIFY COLUMN `program`  longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `calc_form`;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
