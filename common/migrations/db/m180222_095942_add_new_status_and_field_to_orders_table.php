<?php

use yii\db\Migration;

/**
 * Class m180222_095942_add_new_status_and_field_to_orders_table
 */
class m180222_095942_add_new_status_and_field_to_orders_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `orders`
            MODIFY COLUMN `status`  enum('new','payed','calc','payed_api') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'new' AFTER `currency_id`,
            ADD COLUMN `is_police_downloaded`  tinyint NOT NULL AFTER `created_at`;");

        $this->execute("UPDATE `orders` SET `status`='payed_api' WHERE `status`='payed'");
        $this->execute("UPDATE `orders` SET `is_police_downloaded`='1' WHERE `status`='payed_api'");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute("UPDATE `orders` SET `status`='payed' WHERE `status`='payed_api'");

        $this->execute("
        ALTER TABLE `orders`
        MODIFY COLUMN `status`  enum('new','payed','calc') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'new' AFTER `currency_id`,
        DROP COLUMN `is_police_downloaded`;
        ");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180222_095942_add_new_status_and_field_to_orders_table cannot be reverted.\n";

        return false;
    }
    */
}
