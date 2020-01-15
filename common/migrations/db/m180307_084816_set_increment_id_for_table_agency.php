<?php

use yii\db\Migration;

/**
 * Class m180307_084816_set_increment_id_for_table_agency
 */
class m180307_084816_set_increment_id_for_table_agency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0");
        $this->execute("ALTER TABLE `agency` MODIFY COLUMN `id`  int(11) NOT NULL AUTO_INCREMENT FIRST;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0");
        $this->execute("ALTER TABLE `agency` MODIFY COLUMN `id`  int(11) NOT NULL FIRST;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1");
    }


}
