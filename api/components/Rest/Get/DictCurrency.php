<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 17.10.17
 * Time: 16:55
 */

namespace api\components\Rest\Get;

use api\components\Rest\RestMethod;
use common\models\AdditionalCondition;
use common\models\Currency;
use common\models\GeoCountry;
use common\models\InsuranceType;
use common\models\Risk;
use common\models\RiskCategory;

/**
 * Class DictCurrency
 *
 * ### Валюты
 *
 * Тип запроса | URI | Комментарий
 * --- | --- | ---
 * GET | {%api_url}dict/currency | Перечень валют
 *
 * Ответ — массив элементов со следующими атрибутами
 *
 * Ключ | Значение
 * --- | ---
 * id |	Идентификатор валюты
 * name |	Наименование валюты
 * value |	Курс
 * char_code |	ISO 4217 символьный код валюты
 * num_code |	ISO 4217 цифровой код валюты
 * nominal |	Номинал
 *
 * @package api\components\Rest\Get
 */
class DictCurrency extends RestMethod
{
    /** @inheritdoc */
    public $sort_order = 10;
    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /** @inheritdoc */
    public function save(){
        return $this->filterFields(Currency::find()->select("*")->all(),
            ['id', 'name', 'value', 'char_code', 'num_code', 'nominal']);
    }

}