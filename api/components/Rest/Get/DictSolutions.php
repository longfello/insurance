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
use common\components\Calculator\models\travel\FilterSolution;
use common\models\AdditionalCondition;
use common\models\Currency;
use common\models\GeoCountry;
use common\models\InsuranceType;
use common\models\Risk;
use common\models\RiskCategory;

/**
 * Class DictSolutions
 *
 * ### Готовые решения
 *
 * Тип запроса | URI | Комментарий
 * --- | --- | ---
 * GET | {%api_url}dict/solutions | Перечень готовых решений
 *
 * Ответ — массив элементов со следующими атрибутами
 *
 * Ключ | Значение
 * --- | ---
 * id |	Идентификатор готового решения
 * name |	Наименование готового решения
 * description |	Описание готового решения
 *
 * @package api\components\Rest\Get
 */
class DictSolutions extends RestMethod
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
        return $this->filterFields(FilterSolution::find()->where(['is_api' => 1])->all(),
            ['id', 'name', 'description']);
    }

}