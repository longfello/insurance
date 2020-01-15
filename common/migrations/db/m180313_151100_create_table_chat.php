<?php

use yii\db\Migration;

/**
 * Class m180313_151100_create_table_chat
 */
class m180313_151100_create_table_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `chat` (
                `id`  int NOT NULL AUTO_INCREMENT ,
                `id_agency`  int NOT NULL ,
                `from_user_id`  int NOT NULL ,
                `message`  text NULL ,
                `file`  varchar(255) NULL ,
                `create_at`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
                PRIMARY KEY (`id`)
                )ENGINE=InnoDB
                ;"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('chat');
    }

}
