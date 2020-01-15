<?php

use yii\db\Migration;

/**
 * Class m180104_114223_add_cost_interval_id_to_api_sberbank_program_table
 */
class m180104_114223_add_cost_interval_id_to_api_sberbank_program_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute("ALTER TABLE `api_sberbank_program` ADD COLUMN `cost_interval_id`  int(11) AFTER `police_path`;");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->execute("ALTER TABLE `api_sberbank_program` DROP COLUMN `cost_interval_id`");
    }
}
