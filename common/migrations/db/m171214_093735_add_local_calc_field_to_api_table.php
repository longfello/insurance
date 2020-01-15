<?php

use yii\db\Migration;

/**
 * Class m171214_093735_add_local_calc_field_to_api_table
 */
class m171214_093735_add_local_calc_field_to_api_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("
ALTER TABLE `api`
ADD COLUMN `local_calc`  tinyint NOT NULL DEFAULT 0 AFTER `class`,
ADD INDEX `local_calc_idx` (`local_calc`) ;
");
        foreach(\common\models\Api::find()->all() as $one){
            $module = $one->getModule();
            if ($module AND $module->has_local){
                $one->local_calc = 1;
                $one->save();
            }
        }

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute("
ALTER TABLE `api`
DROP COLUMN `local_calc`
");
    }
}
