<?php

use yii\db\Migration;

/**
 * Class m180309_144750_change_table_user_to_agency
 */
class m180309_144750_change_table_user_to_agency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->dropTable('user_to_agency');
        $this->execute("CREATE TABLE `user_to_agency` (
`user_id`  int(11) NOT NULL ,
`agency_id`  int(11) NOT NULL,
PRIMARY KEY (`user_id`, `agency_id`)
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

        $this->dropTable('user_to_agency');
        $this->execute("
                      CREATE TABLE `user_to_agency` (
                `user_id`  int(11) NOT NULL ,
                `agency_id`  int(11) NOT NULL ,
                PRIMARY KEY (`user_id`, `agency_id`)
                )ENGINE = INNODB DEFAULT CHARSET = utf8"
        );
        $this->execute("ALTER TABLE `user_to_agency` ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
        $this->execute("ALTER TABLE `user_to_agency` ADD FOREIGN KEY (`agency_id`) REFERENCES `agency` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
    }



}
