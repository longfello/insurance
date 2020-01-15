<?php

use yii\db\Migration;

/**
 * Class m180302_100502_change_sberbank_risk_implementation
 */
class m180302_100502_change_sberbank_risk_implementation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("RENAME TABLE api_sberbank_risk TO api_sberbank_additional_risk,
             api_sberbank_risk2internal TO api_sberbank_additional_risk2internal,
             api_sberbank_risk2territory TO api_sberbank_additional_risk2territory;");

        $this->execute("ALTER TABLE `api_sberbank_additional_risk` ADD COLUMN `paragraph`  varchar(255) NULL AFTER `name`;");

        $this->execute("CREATE TABLE `api_sberbank_risk` (
            `id`  int NOT NULL AUTO_INCREMENT ,
            `name`  varchar(255) NOT NULL ,
            `paragraph`  varchar(255) NOT NULL ,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB
                DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
                ROW_FORMAT=COMPACT;");

        $this->execute("CREATE TABLE `api_sberbank_risk2internal` (
                `risk_id`  int(11) NOT NULL ,
                `internal_id`  int(11) NOT NULL ,
                PRIMARY KEY (`risk_id`, `internal_id`),
                FOREIGN KEY (`risk_id`) REFERENCES `api_sberbank_risk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (`internal_id`) REFERENCES `risk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                INDEX `internal_id` (`internal_id`) USING BTREE
                )
                ENGINE=InnoDB
                DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
                ROW_FORMAT=COMPACT
            ;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DROP TABLE `api_sberbank_risk2internal`");

        $this->execute("DROP TABLE `api_sberbank_risk`");

        $this->execute("ALTER TABLE `api_sberbank_additional_risk` DROP COLUMN `paragraph`");

        $this->execute("RENAME TABLE api_sberbank_additional_risk TO api_sberbank_risk,
             api_sberbank_additional_risk2internal TO api_sberbank_risk2internal,
             api_sberbank_additional_risk2territory TO api_sberbank_risk2territory;");
    }
}
