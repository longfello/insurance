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
use api\components\Rest\RestComponent;
use api\components\Rest\RestMethod;
use common\components\ApiModule;
use common\components\Calculator\filters\Filter;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\filters\params\travel\FilterParamSum;
use common\components\Calculator\forms\TravelForm;
use common\components\Calculator\models\travel\FilterParam;
use common\models\AdditionalCondition;
use common\models\Api;
use common\models\CostInterval;
use common\models\Currency;
use common\models\GeoCountry;
use common\models\InsuranceType;
use common\models\Orders;
use common\models\Person;
use common\models\Risk;
use common\models\RiskCategory;
use common\modules\geo\models\GeoName;
use frontend\models\PersonInfo;

/**
 * Class PreOrderTravel
 *
 * ### Предварительный заказ полиса
 *
 * Тип запроса | URI | Комментарий
 * --- | --- | ---
 * POST | {%api_url}pre-order/travel | Предварительный заказ полиса туристического страхования
 *
 * Параметры запроса
 *
 * Ключ | Значение | Обязательный | Комметарий
 * --- | --- | --- | ---
 * calc_id | Идентификатор расчёта | + | Возвращается с данными при расчете стоимости полиса
 * travellers | Массив элементов описывающих путешественника в следующем формате: `{  first_name: "", last_name: "", birthday: "", gender: ""}` |	+ |	Все поля обезательные. Имя и фамилия — латинницей. Дата рождения в формате `Y-m-d`. Пол — `male` или `female`
 * payer |	Структура описывающая плательщика в следующем формате: ` { first_name: "", last_name: "", passport_seria: "", passport_no: "",  phone: "", email: "", birthday: "", gender: "", passport: ""} `  | + | Все поля обезательные. Имя и фамилия — латинницей. Дата рождения в формате `Y-m-d`. Пол — `male` или `female`, номер телефона в международном формате
 *
 * Ответ — массив элементов со следующими атрибутами
 *
 * Ключ | Значение
 * --- | ---
 * order_id |	Идентификатор заказа
 * police_url |	URL полиса, если возможно
 * price |	Уточненная стоимость полиса
 * currency_id | Идентификатор валюты
 *
 * @package api\components\Rest\Post
 */
class PreOrderTravel extends RestMethod
{
    /** @var int Идентификатор предварительного расчёта стоимости полиса */
    public $calc_id;

    /** @var Orders Модель заказа */
    public $order;

    /** @var PersonInfo[] Путешественники */
    public $travellers = [[]];

    /** @var PersonInfo Плательщик */
    public $payer;

    /** @inheritdoc */
    public $sort_order = 1010;
    /** @inheritdoc */
    public $accessEnabledBy = [ RestComponent::AUTH_BASIC ];
    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            [['calc_id', 'travellers', 'payer'], 'validateRequired'],
            [['order'], 'validateRequired', 'params' => ['code'=>ErrorCode::NOT_FOUND, 'message' => 'Указанный заказ не найден']],
            [['payer', 'travellers'], 'validateModel'],
            [['order'], 'validateModel'],
            [['order'], 'validateOrderOwner'],
            [['calc_id'], 'integer'],
        ];
    }

    /** @inheritdoc */
    public function initData()
    {
        parent::initData();
        $this->order = Orders::find()->where(['status' => Orders::STATUS_CALC])->andWhere(['id' => $this->calc_id])->one();
        $this->initPayer();
        $this->initTravellers();
    }

    /** @inheritdoc */
    public function save(){
        $module = $this->order->api->getModule();
        $order  = $module->getOrder($this->order->calc_form, $this->order->program->program_id);

        $order->user_id = $this->rest->user->id;
        $order->save();

        return [
            'order_id' => $order->id,
            'police_url' => $order->getPoliceLink(),
            'price' => $order->price,
            'currency_id' => $order->currency_id
        ];
    }

    /** Инициализация полей */
    protected function initPayer(){
        $info = $this->payer;

        $payer = new PersonInfo();
        $payer->scenario = PersonInfo::SCENARIO_PAYER;
        $birthday = isset($info['birthday'])?$info['birthday']:null;
        $birthday = \DateTime::createFromFormat('Y-m-d', trim($birthday));
        $info['birthday'] = $birthday?$birthday->format('d.m.Y'):null;

        $payer->load($info, '');

        $this->payer = $payer;
        $this->order->calc_form->payer = $this->payer;
    }

    /** Инициализация полей */
    protected function initTravellers(){
        $travellers = [];
        foreach ($this->travellers as $info){
            $traveller  = new PersonInfo(['scenario' => PersonInfo::SCENARIO_TRAVELLER]);
            $traveller->first_name = isset($info['first_name'])?$info['first_name']:'';
            $traveller->last_name = isset($info['last_name'])?$info['last_name']:'';
            $birthday = isset($info['birthday'])?$info['birthday']:false;
            if ($birthday){
                $birthday = \DateTime::createFromFormat('Y-m-d', trim($birthday));
                $traveller->birthday =  $birthday->format('d.m.Y');
            }
            $traveller->gender = isset($info['gender'])?$info['gender']:false;
            $traveller->gender = in_array($traveller->gender, ['male', 'female']) ? $traveller->gender : null;
            $travellers[] = $traveller;
        }
        $this->travellers = $travellers;
        $this->order->calc_form->travellers = $this->travellers;
    }
}