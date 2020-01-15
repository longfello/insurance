<?php

use yii\db\Migration;

/**
 * Class m180213_100000_cost_interval_to_innodb
 */
class m180213_100000_cost_interval_to_innodb extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `cost_interval` ENGINE = INNODB');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        try {
            $this->execute('ALTER TABLE `cost_interval` ENGINE = MYISAM');
        } catch (Exception $e) {
            return false;
        }
    }

}
