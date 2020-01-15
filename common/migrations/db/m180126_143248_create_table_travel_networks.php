<?php

use yii\db\Migration;

/**
 * Class m180126_143248_create_table_travel_networks
 */
class m180126_143248_create_table_travel_networks extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("
CREATE TABLE `travel_networks` (
`id`  int NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
;");
        $data = [
            'Сеть "САНМАР"',
            'Сеть "ПЕГАС Туристик"',
            'Сеть "ТУИ"',
            'Сеть "Анекс Шоп"',
            'Сеть "Альянс Туры.ру"',
            'Сеть агентств пляжного отдыха "ВЕЛЛ"',
            'Сеть офисов продаж "Горячие туры"',
            'Сеть "Глобал Тревэл"',
            'Сеть турагентств "Корал Тревэл"',
            'Сеть "Магазинов Горящих Путевок"',
            'Сеть МТК "Спутник"',
            'Сеть "ТБГ"',
            'Не сетевое агентство',
            'Онлайн-сервис',
            'Оператор',
        ];
        foreach ($data as $name){
            $model=new \common\models\TravelNetworks(['name' => $name]);
            $model->save();
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable("travel_networks");
    }
}
