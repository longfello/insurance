<?php

use yii\db\Migration;

/**
 * Class m180227_132037_set_default_to_is_police_downloaded_in_orders
 */
class m180227_132037_set_default_to_is_police_downloaded_in_orders extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `orders` MODIFY COLUMN `is_police_downloaded`  tinyint(4) NOT NULL DEFAULT 0 AFTER `created_at`;");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `orders` MODIFY COLUMN `is_police_downloaded`  tinyint(4) NOT NULL AFTER `created_at`;");
    }
}
