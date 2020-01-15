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
use common\models\GeoCountry;
use common\models\RiskCategory;

/**
 * Class DictRiskCategory
 *
 * ### Категории рисков
 *
 * Тип запроса | URI | Комментарий
 * --- | --- | ---
 * GET | {%api_url}dict/risk-category | Перечень категорий рисков
 *
 * Ответ — массив элементов со следующими атрибутами
 *
 * Ключ | Значение
 * --- | ---
 * id |	Идентификатор категории
 * name |	Наименование категории
 *
 * @package api\components\Rest\Get
 */

class DictRiskCategory extends RestMethod
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
        return $this->filterFields(RiskCategory::find()->select("*")->all(),
            ['id', 'name']);
    }

}