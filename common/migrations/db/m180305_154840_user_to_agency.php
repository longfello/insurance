<?php

use yii\db\Migration;

/**
 * Class m180305_154840_user_to_agency
 */
class m180305_154840_user_to_agency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    { 
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

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_to_agency');
    }


}
