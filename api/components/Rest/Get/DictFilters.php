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
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\models\travel\FilterParam;
use common\models\GeoCountry;
use common\models\Risk;
use common\models\RiskCategory;

/**
 * Class DictRisk
 *
 * ### Фильтры
 *
 * <i>Фильтры могут быть использованы для уточнения параметров подбора программ страхования</i>
 *
 * Тип запроса | URI | Комментарий
 * --- | --- | ---
 * GET | {%api_url}dict/filters | Перечень фильтров
 *
 * Ответ — массив элементов со следующими атрибутами
 *
 * Ключ | Значение
 * --- | ---
 * id |	Идентификатор фильтра
 * category_id |	Идентификатор категории риска, если фильтр соответствует риску
 * name |	Наименование фильтра
 * description |	Описание фильтра
 * params |	Параметры фильтра
 *
 * @package api\components\Rest\Get
 */
class DictFilters extends RestMethod
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
        return $this->filterFields(FilterParam::find()->select("*")->all(),
            ['id', 'category_id' => function($model){
                /** @var $model FilterParam */
                return ($model->risk && $model->risk->category)?$model->risk->category->id:null;

            }, 'name', 'description' => function($model){
                /** @var $model FilterParam */
                return ($model->risk)?$model->risk->description:null;
            }, 'params' => function($model){
                /** @var $model FilterParam */
                $handler = $model->handler;
                /** @var $handler FilterParamPrototype */
              if ($handler && $handler->availableVariantVariables){
                  return $handler->availableVariantVariables;
              }
              return null;
            }]);
    }

}