<?php

use yii\db\Migration;

/**
 * Handles the creation of table `api_sberbank_territory`.
 */
class m180103_124141_create_api_sberbank_territory_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute("CREATE TABLE `api_sberbank_territory` (
        `id`  int NOT NULL AUTO_INCREMENT ,
        `insTerritory`  varchar(255) NOT NULL COMMENT 'Территория' ,
        `name`  varchar(255) NOT NULL COMMENT 'Название территории' ,
        `enabled`  tinyint NOT NULL DEFAULT 1 COMMENT 'Разрешена' ,
        PRIMARY KEY (`id`)
        )
        ENGINE=InnoDB;
        ");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("DROP TABLE `api_sberbank_territory`;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
