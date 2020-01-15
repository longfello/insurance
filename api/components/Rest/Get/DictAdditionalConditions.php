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
use common\models\GeoCountry;
use common\models\Risk;
use common\models\RiskCategory;

/**
 * Class DictAdditionalConditions
 *
 * ### Дополнительные условия
 *
 * Тип запроса | URI | Комментарий
 * --- | --- | ---
 * GET | {%api_url}dict/additional-conditions | Перечень дополнительных условий
 *
 * Ответ — массив элементов со следующими атрибутами
 *
 * Ключ | Значение
 * --- | ---
 * id | Идентификатор дополнительного условия
 * name | Наименование
 * description | Описание
 * slug | Псевдоним
 *
 * @package api\components\Rest\Get
 */
class DictAdditionalConditions extends RestMethod
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
        return $this->filterFields(AdditionalCondition::find()->select("*")->all(),
            ['id', 'name', 'description', 'slug']);
    }

}