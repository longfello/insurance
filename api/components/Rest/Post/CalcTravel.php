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

namespace api\components\Rest\Post;

use api\components\ErrorCode;
use api\components\Rest\RestMethod;
use common\components\ApiModule;
use common\components\Calculator\filters\Filter;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\filters\params\travel\FilterParamSum;
use common\components\Calculator\forms\TravelForm;
use common\components\Calculator\models\travel\FilterParam;
use common\components\Calculator\models\travel\FilterSolution;
use common\models\AdditionalCondition;
use common\models\Api;
use common\models\CostInterval;
use common\models\Currency;
use common\models\GeoCountry;
use common\models\InsuranceType;
use common\models\ProgramResult;
use common\models\Risk;
use common\models\RiskCategory;
use common\modules\geo\models\GeoName;

/**
 * Class CalcTravel
 *
 * ### Расчет стоимости полиса
 *
 * Тип запроса | URI | Комментарий
 * --- | --- | ---
 * POST | {%api_url}calc/travel | Расчет полисов туристического страхования
 *
 * Параметры запроса для туристического страхования
 *
 * Ключ | Значение | Обязательный | Комметарий
 * --- | --- | --- | ---
 * country | значение или массив значений <slug> или <iso_alpha2> или <iso_alpha3> из справочника стран | + |
 * date_from | Дата начала путешествия |	+ |	Y-m-d
 * date_to |	Дата окончания путешествия |	+ |	Y-m-d
 * solution_id | Идентификатор готового решения | - | Параметры фильтра готового решения могут быть переназначены параметрами из запроса
 * travellers | Массив вида: ``` [{ first_name: «name», last_name: «name», age: 18 }, …  ] ``` все поля являются необязательными | - | По-умолчанию: ``` [ { } ] ``` - (один совершеннолетний путешественник)
 * insurant |	Массив идентификаторов страхователей в формате запроса |	- |	По умолчанию — все, с fast_calc = 1
 * filters |	Массив идентификаторов фильтров. Если фильтр предполагает параметр — передавать его в виде массива c элементами filter_id и params. Например для формата `json` `{filter_id:24, params: {"amount": 2000, "sick-list": 1}}`. <a href="#dict_filters">Подробнее о фильтрах</a>. Формат запроса равен формату ответа. | - | По умолчанию — не применяются ограничения рисков
 *
 * Ответ — массив элементов со следующими атрибутами
 *
 * Ключ | Значение
 * --- | ---
 * calc_id |	Идентификатор расчёта
 * insurant_id |	Идентификатор страхователя
 * rule_url |	Правила страхования
 * police_url |	Пример полиса
 * risks |	Перечень страховых рисков
 * cost |	Стоимость полиса
 * currency_id |	Идентификатор валюты
 *
 * @package api\components\Rest\Post
 */
class CalcTravel extends RestMethod
{
    /** @inheritdoc */
    public $sort_order = 1000;
    /** @var GeoCountry[] Choosed country */
    public $country = [];
    /** @var \DateTime Start travel date */
    public $date_from;
    /** @var \DateTime End travel date */
    public $date_to;
    /** @var array[] Travellers description */
    public $travellers = [[]];
    /** @var Api[] Choosed insurant model or null if any */
    public $insurant = null;
    /** @var array[] Choosed risks and own variants */
    public $filters = [];
    /** @var TravelForm Calculator form */
    public $calcForm;
    /** @var FilterSolution|null */
    public $solution;
    /** @var int Идентификатор готового решения */
    public $solution_id;
    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            [['country', 'date_from', 'date_to'], 'validateRequired'],
            ['calcForm', 'validateModel'],
            [['insurant', 'filters', 'travellers', 'solution_id'], 'safe'],
        ];
    }

    /** @inheritdoc */
    public function initData()
    {
        parent::initData();

        $this->country     = GeoCountry::find()->where(['OR', ['slug' => $this->country], ['iso_alpha2' => $this->country], ['iso_alpha3' => $this->country]])->all();
        $this->date_from   = \DateTime::createFromFormat('Y-m-d', $this->date_from);
        $this->date_to     = \DateTime::createFromFormat('Y-m-d', $this->date_to);
        $this->solution_id = isset($this->data['solution_id'])?$this->data['solution_id']:null;

        $data          = $this->filters;
        $this->filters = [];
        foreach ($data as $one){
            if (isset($one['filter_id'])){
                $filter = FilterParam::findOne(['id' => $one['filter_id']]);
                if ($filter){
                    if ($filter->handler){
                        $one['params'] = isset($one['params'])?$one['params']:[];
                        if (isset($one['params'][FilterParamPrototype::PARAM_SLUG_SIMPLE])){
                            $one['params'] = array_pop($one['params']);
                        }
                        $filter->handler->load([
                            $filter->handler->param->id => $one['params']
                        ]);
                        $this->filters[$filter->handler->slug] = $filter;
                    }
                } else \Yii::$app->response->throwError(ErrorCode::PARSE_DATA, "Не зарегистрирован фильтр с filter_id = {$one['filter_id']}");
            } else \Yii::$app->response->throwError(ErrorCode::PARSE_DATA, "Не определен filter_id в разделе filters");
        }

        // Инициализация страховой суммы минимальной, если она не задана
        if (!isset($this->filters[FilterParamSum::SLUG_SUM])){
            $filter = FilterParam::findOne(['id' => 1]);
            if ($filter){
                if ($filter->handler){
                    $one['params'] = $filter->handler->param->id;
                    $filter->handler->load([
                        $filter->handler->param->id => $one['params']
                    ]);
                    $this->filters[$filter->handler->slug] = $filter;
                }
            } else \Yii::$app->response->throwError(ErrorCode::INTERNAL_ERROR, "Ошибка установки минимальной страховой суммы");
        }

        // инициализация готовых решений
        if ($this->solution_id) {
            if (!$this->solution = FilterSolution::find()->where(['id' => $this->solution_id, 'is_api' => 1])->one()){
                \Yii::$app->response->throwError(ErrorCode::VALIDATE_DATA, "Готовое решение с идентификатором #{$this->solution_id} не зарегистрировано");
            }
        }

        // инициализация API
        $this->insurant = $this->insurant ? Api::find()->where(['id' => $this->insurant, 'enabled' => 1])->all()
                                          : Api::find()->where(['local_calc' => 1, 'enabled' => 1])->all();

        $this->calcForm = new TravelForm();
        $this->calcForm->loadFromApi($this);
    }

    /** @inheritdoc */
    public function save(){
        $filter = new Filter(['form' => $this->calcForm]);
        $result = $filter->getPropositions([
            'user_id' => $this->rest->user?$this->rest->user->id:null
        ]);

        return $this->compilePropositions($result);
    }

    public function compilePropositions($propositions){
        foreach ($propositions as $key => $item){
            if ($item['cost'] == 0){
                unset($propositions[$key]);
            }
        }

        $result = [];
        foreach ($propositions as $one){
            /** @var $one ProgramResult */
            $result[] = [
                'calc_id'     => $one->order_id,
                'insurant_id' => $one->api_id,
                'rule_url'    => $one->rule_url,
                'police_url'  => $one->police_url,
                'risks'       => $one->risks,
                'cost'        => $one->cost,
                'currency_id' => Currency::findOne(['char_code' => Currency::RUR])->id
            ];
        }

        return $result?$result:[];
    }

}