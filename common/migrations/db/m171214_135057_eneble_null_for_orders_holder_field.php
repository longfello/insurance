<?php

use yii\db\Migration;

/**
 * Class m171214_135057_eneble_null_for_orders_holder_field
 */
class m171214_135057_eneble_null_for_orders_holder_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("ALTER TABLE `orders` MODIFY COLUMN `holder_id`  int(11) NULL AFTER `status`;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("DELETE FROM `orders` WHERE `holder_id` IS NULL;");
        $this->execute("ALTER TABLE `orders` MODIFY COLUMN `holder_id`  int(11) NOT NULL AFTER `status`;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
