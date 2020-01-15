<?php

use yii\db\Migration;

/**
 * Class m171228_131454_create_table_oreder_process_state
 */
class m171228_131454_create_table_oreder_process_state extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `order_process_state` (
`slug`  char(64) NOT NULL ,
`data`  mediumtext NOT NULL ,
`order_id`  int(11) NULL ,
`created_at`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`slug`),
FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute("DROP TABLE `order_process_state`;");
    }

}
