<?php

use yii\db\Migration;

/**
 * Class m180309_083255_add_fields_to_user_profile
 */
class m180309_083255_add_fields_to_user_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `user_profile`ADD COLUMN `city` varchar(255) NULL AFTER `lastname`, ADD COLUMN `phone`  varchar(255) NULL AFTER `city`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `user_profile` DROP COLUMN `city` , DROP COLUMN `phone` ;");
    }


}
