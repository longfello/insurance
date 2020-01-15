<?php

use yii\db\Migration;

/**
 * Class m180103_123128_change_api_sberbank_program_table_engine
 */
class m180103_123128_change_api_sberbank_program_table_engine extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("ALTER TABLE `api_sberbank_program` ENGINE=InnoDB;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        echo "m180103_123128_change_api_sberbank_program_table_engine cannot be reverted.\n";

        return false;
    }

}
