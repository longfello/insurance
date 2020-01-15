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
use common\models\InsuranceType;
use common\models\Risk;
use common\models\RiskCategory;

/**
 * Class DictInsuranceType
 *
 * ### Типы страхования
 *
 * Тип запроса | URI | Комментарий
 * --- | --- | ---
 * GET | {%api_url}dict/insurance-type |  	Перечень типов страхования
 *
 * Ответ — массив элементов со следующими атрибутами
 *
 * Ключ | Значение
 * --- | ---
 * id |	Идентификатор типа страхования
 * slug |	Псевдоним типа страхования
 * name |	Наименование типа страхования
 * description |	Описание типа страхования
 *
 * @package api\components\Rest\Get
 */
class DictInsuranceType extends RestMethod
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
        return $this->filterFields(InsuranceType::find()->select("*")->where(['enabled' => 1])->all(),
            ['id', 'name', 'description', 'slug']);
    }

}