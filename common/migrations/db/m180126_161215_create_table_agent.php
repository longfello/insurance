<?php

use yii\db\Migration;

/**
 * Class m180126_161215_create_table_agent
 */
class m180126_161215_create_table_agent extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("
CREATE TABLE `agency` (
`id`  int NOT NULL ,
`travel_network_id`  int NOT NULL COMMENT 'Сеть туристических агентств' ,
`company_type`  enum('ooo','zao','ip') NOT NULL COMMENT 'Организационная форма компании' ,
`company_tax_type`  enum('easy','common') NOT NULL COMMENT 'Форма налогооблажения' ,
`cooperation_form`  enum('contract','iframe','api') NOT NULL COMMENT 'Форма Сотрудничества' ,
`chief_name`  varchar(255) NOT NULL COMMENT 'ФИО Руководителя' ,
`chief_position`  varchar(255) NOT NULL COMMENT 'Должность Руководителя' ,
`name`  varchar(255) NOT NULL COMMENT 'Наименование компании (без орг.формы' ,
`legal_region`  varchar(255) NULL COMMENT 'Юридический адрес - регион' ,
`legal_city`  varchar(255) NOT NULL COMMENT 'Юридический адрес - город' ,
`legal_address`  varchar(255) NOT NULL COMMENT 'Юридический адрес' ,
`legal_index`  varchar(255) NOT NULL COMMENT 'Юридический адрес - индекс' ,
`actual_region`  varchar(255) NULL COMMENT 'Фактический адрес - регион' ,
`actual_city`  varchar(255) NOT NULL COMMENT 'Фактический адрес - город' ,
`actual_address`  varchar(255) NOT NULL COMMENT 'Фактический адрес' ,
`actual_index`  varchar(255) NOT NULL COMMENT 'Фактический адрес - индекс' ,
`phone`  varchar(255) NOT NULL COMMENT 'Телефон' ,
`email`  varchar(255) NOT NULL COMMENT 'Email' ,
`inn`  varchar(12) NOT NULL COMMENT 'ИНН' ,
`kpp`  varchar(9) NULL COMMENT 'КПП' ,
`ogrn`  varchar(16) NOT NULL COMMENT 'ОГРН/ОГРНИП' ,
`okved`  varchar(255) NULL COMMENT 'ОКВЭД' ,
`okpo`  varchar(10) NULL COMMENT 'ОКПО' ,
`okato`  varchar(11) NULL COMMENT 'ОКАТО' ,
`checking_account`  varchar(20) NOT NULL COMMENT 'Расчетный счет' ,
`bank`  varchar(255) NOT NULL COMMENT 'Банк' ,
`correspondent_account`  varchar(20) NOT NULL COMMENT 'Кор. счет' ,
`bik`  varchar(9) NOT NULL COMMENT 'БИК' ,
`href`  varchar(255) NULL COMMENT 'Адрес сайта' ,
`comment`  text NULL COMMENT 'Комментарии' ,
PRIMARY KEY (`id`),
FOREIGN KEY (`travel_network_id`) REFERENCES `travel_networks` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
)
ENGINE=InnoDB;");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('agency');
    }
}
