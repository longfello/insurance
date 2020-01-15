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
use common\components\ApiModule;
use common\models\AdditionalCondition;
use common\models\Api;
use common\models\CostInterval;
use common\models\Currency;
use common\models\GeoCountry;
use common\models\InsuranceType;
use common\models\Risk;
use common\models\RiskCategory;

/**
 * Class DictInsurant
 *
 * ### Страхователи
 *
 * Тип запроса | URI | Комментарий
 * --- | --- | ---
 * GET | {%api_url}dict/insurant | Перечень страхователей
 *
 * Ответ — массив элементов со следующими атрибутами
 *
 * Ключ | Значение
 * --- | ---
 * id |	Идентификатор страхователя
 * name |	Наименование страхователя
 * description |	Описание страхователя
 * fast_calc | Признак быстрого предварительного расчета (0/1)
 *
 * @package api\components\Rest\Get
 */
class DictInsurant extends RestMethod
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
        return $this->filterFields(Api::find()->select("*")->all(),
            [
                'id', 'name', 'description',
                'fast_calc' => function($model){
                    /** @var $model Api */
                    $api = $model->getModule();
                    /** @var $module ApiModule */
                    return $api->has_local?1:0;
                }
            ]);
    }

}