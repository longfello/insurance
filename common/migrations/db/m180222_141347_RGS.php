<?php

use yii\db\Migration;

/**
 * Class m180222_141347_RGS
 */
class m180222_141347_RGS extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0");

        // Tables structure
        $this->execute("CREATE TABLE `api_rgs_product` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ext_id` char(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
            `title` varchar(35) NOT NULL,
            `rule_path` varchar(1024) DEFAULT NULL,
            `rule_base_url` varchar(1024) DEFAULT NULL,
            `police_path` varchar(1024) DEFAULT NULL,
            `police_base_url` varchar(1024) DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `ext_id` (`ext_id`(1)) USING BTREE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_classifier` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ext_id` char(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
            `title` varchar(35) NOT NULL,
            `class` varchar(60) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `ext_id` (`ext_id`(2)) USING BTREE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_currency` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ext_id` char(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
            `title` varchar(3) NOT NULL,
            `default` tinyint(1) NOT NULL DEFAULT '0',
            `enabled` tinyint(1) NOT NULL DEFAULT '1',
            PRIMARY KEY (`id`),
            UNIQUE KEY `ext_id` (`ext_id`(2)) USING BTREE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_territory_type` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ext_id` char(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
            `title` varchar(120) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `ext_id` (`ext_id`(3)) USING BTREE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_country` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ext_id` char(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
            `title` varchar(125) NOT NULL,
            `territory_type_id` int(11) DEFAULT NULL,
            `min_sum` int(11) NOT NULL DEFAULT '0',
            `enabled` tinyint(1) NOT NULL DEFAULT '1',
            PRIMARY KEY (`id`),
            UNIQUE KEY `ext_id` (`ext_id`(4)) USING BTREE,
            KEY `territory_type_id` (`territory_type_id`),
            CONSTRAINT `api_rgs_country_ibfk_1` FOREIGN KEY (`territory_type_id`) REFERENCES `api_rgs_territory_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_risk_type` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ext_id` char(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
            `title` varchar(50) NOT NULL,
            `main` tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`),
            UNIQUE KEY `ext_id` (`ext_id`(1)) USING BTREE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_program` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ext_id` char(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
            `title` varchar(35) NOT NULL,
            `risk_type_id` int(11) NOT NULL,
            `enabled` tinyint(1) NOT NULL DEFAULT '1',
            PRIMARY KEY (`id`),
            UNIQUE KEY `ext_id` (`ext_id`(4)) USING BTREE,
            KEY `api_rgs_program_ibfk_1` (`risk_type_id`),
            CONSTRAINT `api_rgs_program_ibfk_1` FOREIGN KEY (`risk_type_id`) REFERENCES `api_rgs_risk_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_sum` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ext_id` char(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
            `title` varchar(12) NOT NULL,
            `sum` int(11) NOT NULL,
            `program_id` int(11) NOT NULL,
            `enabled` tinyint(1) NOT NULL DEFAULT '1',
            `manual` tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`),
            UNIQUE KEY `ext_id` (`ext_id`(3)) USING BTREE,
            KEY `program_id` (`program_id`),
            CONSTRAINT `api_rgs_sum_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `api_rgs_program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_additional_condition_type` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ext_id` char(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
            `title` varchar(45) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `ext_id` (`ext_id`(2)) USING BTREE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_additional_condition` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ext_id` char(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
            `title` varchar(75) NOT NULL,
            `additional_condition_type_id` int(11) NOT NULL,
            `default` tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`),
            UNIQUE KEY `ext_id` (`ext_id`(2)) USING BTREE,
            KEY `additional_condition_type_id` (`additional_condition_type_id`),
            CONSTRAINT `api_rgs_additional_condition_ibfk_1` FOREIGN KEY (`additional_condition_type_id`) REFERENCES `api_rgs_additional_condition_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_currency2dict` (
            `internal_id` int(11) NOT NULL,
            `currency_id` int(11) NOT NULL,
            KEY `internal_id` (`internal_id`),
            KEY `currency_id` (`currency_id`),
            CONSTRAINT `api_rgs_currency2dict_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `api_rgs_currency` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `api_rgs_currency2dict_ibfk_2` FOREIGN KEY (`internal_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_country2dict` (
            `internal_id` int(11) NOT NULL,
            `country_id` int(11) NOT NULL,
            KEY `internal_id` (`internal_id`),
            KEY `country_id` (`country_id`),
            CONSTRAINT `api_rgs_country2dict_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `api_rgs_country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `api_rgs_country2dict_ibfk_2` FOREIGN KEY (`internal_id`) REFERENCES `geo_country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_sum2dict` (
            `internal_id` int(11) NOT NULL,
            `sum_id` int(11) NOT NULL,
            KEY `internal_id` (`internal_id`),
            KEY `sum_id` (`sum_id`),
            CONSTRAINT `api_rgs_sum2dict_ibfk_1` FOREIGN KEY (`internal_id`) REFERENCES `cost_interval` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `api_rgs_sum2dict_ibfk_2` FOREIGN KEY (`sum_id`) REFERENCES `api_rgs_sum` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_program_risk` (
            `program_id` int(11) NOT NULL,
            `risk_id` int(11) NOT NULL,
            KEY `program_id` (`program_id`),
            KEY `risk_id` (`risk_id`),
            CONSTRAINT `api_rgs_program_risk_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `api_rgs_program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `api_rgs_program_risk_ibfk_2` FOREIGN KEY (`risk_id`) REFERENCES `risk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->execute("CREATE TABLE `api_rgs_additional_condition_type_risk` (
            `additional_condition_type_id` int(11) NOT NULL,
            `risk_id` int(11) NOT NULL,
            KEY `additional_condition_type_id` (`additional_condition_type_id`),
            KEY `risk_id` (`risk_id`),
            CONSTRAINT `api_rgs_additional_condition_type_risk_ibfk_1` FOREIGN KEY (`additional_condition_type_id`) REFERENCES `api_rgs_additional_condition_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `api_rgs_additional_condition_type_risk_ibfk_2` FOREIGN KEY (`risk_id`) REFERENCES `risk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        // Tables data
        $this->execute("INSERT INTO `api` (name, class, local_calc, rate_expert, rate_asn, enabled, thumbnail_base_url, thumbnail_path, actions, description, service_center_url) VALUES ('Росгосстрах', 'common\\\modules\\\ApiRgs\\\Module', 0, 'ruBBB+', '44.54', 1, '" . getenv('STORAGE_URL') . "/source', '1/LIDvE3OoG7hkOv4r6OjEyX4_BW6OobNo.gif', '', '', '')");
        $this->execute("INSERT INTO `api_rgs_product` VALUES (1,'7463234b-fbdc-4830-9b37-94b365afa129','ВЗР_174_bullosafe.ru_сайт партнера',NULL,NULL,NULL,NULL)");
        $this->execute("INSERT INTO `api_rgs_classifier` VALUES (1,'771254C3-CF3D-4A53-BD2F-0871264E71AB','Страны','\\\common\\\modules\\\ApiRgs\\\models\\\Country'),(2,'5533EEBE-A8AF-4F25-A900-7A16E3AE0CBB','Территории','\\\common\\\modules\\\ApiRgs\\\models\\\TerritoryType'),(3,'63665791-125E-46E7-878B-7E625EA62803','Валюты','\\\common\\\modules\\\ApiRgs\\\models\\\Currency'),(4,'99A33329-7653-4DD1-B0AD-32C762FDD8A1','Виды рисков','\\\common\\\modules\\\ApiRgs\\\models\\\RiskType'),(5,'08FBE6DE-FD04-41A5-AD23-8C5E8EF546D4','Программа страхования','\\\common\\\modules\\\ApiRgs\\\models\\\Program'),(6,'CC1CF22B-82DF-4AFE-8B0B-5614FC80E2D3','Cтраховые суммы','\\\common\\\modules\\\ApiRgs\\\models\\\Sum'),(7,'368A05A1-FA4A-4089-9602-5BE46BB99F33','Вид дополнительного условия','\\\common\\\modules\\\ApiRgs\\\models\\\AdditionalConditionType'),(8,'64DDB7CF-21BD-4173-BBDF-903F190A11C9','Дополнительное условие','\\\common\\\modules\\\ApiRgs\\\models\\\AdditionalCondition')");

        $this->execute("SET FOREIGN_KEY_CHECKS = 1");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0");

        $this->execute('DROP TABLE IF EXISTS `api_rgs_additional_condition_type_risk`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_program_risk`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_sum2dict`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_country2dict`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_currency2dict`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_additional_condition`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_additional_condition_type`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_sum`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_program`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_risk_type`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_country`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_territory_type`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_currency`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_classifier`');
        $this->execute('DROP TABLE IF EXISTS `api_rgs_product`');

        $this->execute("SET FOREIGN_KEY_CHECKS = 1");
    }

}
