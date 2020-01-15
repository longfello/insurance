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
use common\models\CostInterval;
use common\models\Currency;
use common\models\GeoCountry;
use common\models\InsuranceType;
use common\models\Risk;
use common\models\RiskCategory;

/**
 * Class DictCostInterval
 *
 * ### Интервалы страховых сумм
 *
 * Тип запроса | URI | Комментарий
 * --- | --- | ---
 * GET | {%api_url}dict/cost-interval | Перечень интервалов страховыс сумм
 *
 * Ответ — массив элементов со следующими атрибутами
 *
 * Ключ | Значение
 * --- | ---
 * id | Идентификатор интервала
 * name | Наименование интервала
 * description | Описание интервала
 * min_amount | Начальная сумма интервала, EUR
 * max_amount | Конечная сумма интервала, EUR
 *
 * @package api\components\Rest\Get
 */

class DictCostInterval extends RestMethod
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
        return $this->filterFields(CostInterval::find()->select("*")->all(),
            ['id', 'name', 'description', 'from' => 'min_amount', 'to' => 'max_amount']);
    }

}