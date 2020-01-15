<?php

use yii\db\Migration;

/**
 * Handles the creation of table `api_sberbank_program`.
 */
class m180103_113020_create_api_sberbank_program_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute("CREATE TABLE `api_sberbank_program` (
        `id`  int NOT NULL AUTO_INCREMENT COMMENT 'Id ' ,
        `insProgram`  varchar(255) NOT NULL ,
        `name`  varchar(255) NOT NULL ,
        `rule_base_url`  varchar(1024) NULL ,
        `rule_path`  varchar(1024) NULL ,
        `police_base_url`  varchar(1024) NULL ,
        `police_path`  varchar(1024) NULL ,
        PRIMARY KEY (`id`)
        )");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("DROP TABLE `api_sberbank_program`;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
