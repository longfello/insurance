<?php

use yii\db\Migration;

/**
 * Handles the creation of table `api_sberbank_program2risk`.
 */
class m180103_120855_create_api_sberbank_program2risk_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("
            CREATE TABLE `api_sberbank_program2risk` (
            `program_id`  int(11) NOT NULL ,
            `risk_id`  int(11) NOT NULL ,
            `summa`  decimal(10,2) NOT NULL DEFAULT 0.00 ,
            `is_optional`  int(2) NOT NULL DEFAULT 0 COMMENT 'Опциональный риск' ,
            `name`  varchar(255) NOT NULL ,
            PRIMARY KEY (`program_id`, `risk_id`),
            FOREIGN KEY (`risk_id`) REFERENCES `risk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (`program_id`) REFERENCES `api_sberbank_program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            INDEX `api_sberbank_program2risk_ibfk_2` (`risk_id`) USING BTREE
            )
            ENGINE=InnoDB
            DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
            ROW_FORMAT=COMPACT
            ;
        ");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("DROP TABLE `api_sberbank_program2risk`;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
