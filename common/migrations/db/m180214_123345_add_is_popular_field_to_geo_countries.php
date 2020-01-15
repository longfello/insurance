<?php

use yii\db\Migration;

/**
 * Class m180214_123345_add_is_popular_field_to_geo_countries
 */
class m180214_123345_add_is_popular_field_to_geo_countries extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `geo_country` ADD COLUMN `is_popular`  tinyint NOT NULL DEFAULT 0 COMMENT 'Популярная страна' AFTER `shengen`");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `geo_country` DROP COLUMN `is_popular`");
    }
}
