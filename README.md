# BulloSafe

Сайт основан на сборке [Yii2 Starter Kit](https://github.com/trntv/yii2-starter-kit)  

Редакция 1.0 - актуально на 01/10/2017

## СОДЕРЖАНИЕ
- [О Yii Starter Kit](#о-yii-starter-kit)
- [Архитектура проекта](#архитектура-проекта)
    - [Архитектура API туристического страхования](#архитектура-api-туристического-страхования)

##О Yii Starter Kit
[Read More](https://github.com/trntv/yii2-starter-kit)

## Архитектура проекта

## Архитектура API туристического страхования
API туристического страхования изначально описываются в таблице Api. После этого будет происходить взаимодействие с данным модулем (если поле enabled=1).
Общая структура модуля следующая:

|Элемент файловой системы                                       | Описание |
|common/modules/Api<псевдоним-апи>                              | Папка модуля API |
|common/modules/Api<псевдоним-апи>/components/                  | Папка для компонентов API. В том числе классы условий поиска |
|common/modules/Api<псевдоним-апи>/components/ProgramSearch.php | Класс поиска программ страхования | 
|common/modules/Api<псевдоним-апи>/models/*.php                 | Модели ActiveRecord |
|common/modules/Api<псевдоним-апи>/views/*.php                  | Представления (если необходимы) |
|common/modules/Api<псевдоним-апи>/Module.php                   | Класс модуля |

### Класс модуля
Должен наследоваться от common\components\ApiModule 
Переназначать следующие свойства:
```PHP
public $has_local = true;  // Признак рассчета стоимости на сервере
```

и реализовать следующие методы:

```PHP
/**
 * Поиск программ страхования по заданным критериям  
 * @return null|\common\models\ProgramResult 
 */
public function search(\common\components\Calculator\forms\TravelForm $form )

/**
 * Создает и возвращает заказ по заданым критериям   
 * @return false|\common\models\Orders
 */
public function getOrder(\common\components\Calculator\forms\TravelForm $form, $program_id)

/**
 * Оформляет полис указанного заказа на стороне удаленного API    
 * @return null
 */
public function buyOrder(\common\models\Orders $order)

/**
 * Скачивает полис указанного заказа с API   
 * @return string[] Лог операции. Ключ - timestamp, значение - строка
 */
public function downloadOrder(\common\models\Orders $order, $additionalInfo = null){

/**
 * Возвращает массив для формирования меню \yii\widgets\Menu    
 * @return array[]
 */
public static function getAdminMenu(){

/**
 * Расчитывает стоимость указанного полиса. Возможно два варианта расчета: self::CALC_LOCAL - локальный, self::CALC_API - расчет на стороне API      
 * @return float|int
 */
abstract function calcPrice($program, $form, $calc_type = self::CALC_LOCAL);

/**
 * Возвращает программу по её id      
 * @return mixed
 */
abstract function getProgram($program_id);
```

### Класс поиска программ страхования
  
Не нормируемый класс, используется в классе модуля для выноса логики поиска.

### Модели ActiveRecord

Модели ActiveRecord не нормативны. На усмотрение разработчика реализуются в достаточном количестве и объеме для реализации функционала.


## Калькулятор
Калькулятор реализован в виде компонента common/components/Calculator/Calculator.php

Структура следующая:

|Элемент файловой системы             | Описание |
|-------------------------------------|----------|
|common/components/Calculator/filters | Фильтры и их параметры |
|common/components/Calculator/forms   | Модели форм |
|common/components/Calculator/models  | Модели ActiveRecord |
|common/components/Calculator/widgets | Виджеты форм калькуляторов |


## PS
Код старались делать самодокументируемым phpDoc
