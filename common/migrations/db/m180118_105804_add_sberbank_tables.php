<?php

use yii\db\Migration;

/**
 * Class m180118_105804_add_sberbank_tables
 */
class m180118_105804_add_sberbank_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("
CREATE TABLE `api_sberbank_risk` (
`id`  int NOT NULL AUTO_INCREMENT ,
`slug`  varchar(50) NOT NULL ,
`name`  varchar(100) NOT NULL ,
PRIMARY KEY (`id`) 
) ENGINE=InnoDB;");
        $this->execute("
CREATE TABLE `api_sberbank_risk2internal` (
`risk_id`  int(11) NOT NULL ,
`internal_id`  int(11) NOT NULL ,
PRIMARY KEY (`risk_id`, `internal_id`),
FOREIGN KEY (`risk_id`) REFERENCES `api_sberbank_risk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`internal_id`) REFERENCES `risk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB ;
");
        $this->execute("
CREATE TABLE `api_sberbank_risk2territory` (
`risk_id`  int(11) NOT NULL ,
`territory_id`  int(11) NOT NULL ,
PRIMARY KEY (`risk_id`, `territory_id`),
FOREIGN KEY (`risk_id`) REFERENCES `api_sberbank_risk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`territory_id`) REFERENCES `api_sberbank_territory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB ;
");
        $data = [
            ['id' => 1, 'slug' => 'isOptionSport',       'name' => 'Спортивный'],
            ['id' => 2, 'slug' => 'isOptionBaggage',     'name' => 'Защита багажа'],
            ['id' => 3, 'slug' => 'isOptionSpecialCase', 'name' => 'Особый случай'],
            ['id' => 4, 'slug' => 'isOptionLawyer',      'name' => 'Личный адвокат'],
            ['id' => 5, 'slug' => 'isOptionPrudent',     'name' => 'Предусмотрительный'],
        ];
        foreach ($data as $one){
            $model = new \common\modules\ApiSberbank\models\AdditionalRisk($one);
            $model->save();
        }
        $data = [
            ['risk_id' => 1, 'internal_id' => 21],
            ['risk_id' => 2, 'internal_id' => 19],
            ['risk_id' => 2, 'internal_id' => 20],
            ['risk_id' => 4, 'internal_id' => 15]
        ];
        foreach ($data as $one){
            $model = new \common\modules\ApiSberbank\models\AdditionalRisk2internal($one);
            try{
                $model->save();
            } catch (\Exception $e){

            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("DROP TABLE IF EXISTS `api_sberbank_risk2internal`");
        $this->execute("DROP TABLE IF EXISTS `api_sberbank_risk2territory`");
        $this->execute("DROP TABLE IF EXISTS `api_sberbank_risk`");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
