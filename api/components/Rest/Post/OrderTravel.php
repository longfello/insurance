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
use common\models\Risk;
use common\models\RiskCategory;
use common\modules\geo\models\GeoName;

/**
 * Class OrderTravel
 *
 * ### Подтверждение оплаты полиса
 *
 * Тип запроса | URI | Комментарий
 * --- | --- | ---
 * POST | {%api_url}order/travel | Подтверждение оплаты полиса
 *
 * Параметры запроса
 *
 * Ключ | Значение | Обязательный | Комметарий
 * --- | --- | --- | ---
 * order_id | Идентификатор заказа | + | Возвращается с данными при предварительном расчете
 *
 * Ответ — массив элементов со следующими атрибутами
 *
 * Ключ | Значение
 * --- | ---
 * police_url |	URL полиса, если возможно
 * price |	Уточненная стоимость полиса
 * currency_id | Идентификатор валюты
 *
 * @package api\components\Rest\Post
 */
class OrderTravel extends RestMethod
{
    /** @inheritdoc */
    public $sort_order = 1020;

    /** @var int Идентификатор заказа */
    public $order_id;
    /** @var Orders Модель заказа */
    public $order;
    /** @inheritdoc */
    public $accessEnabledBy = [ RestComponent::AUTH_BASIC ];
    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            [['order_id'], 'integer'],
            [['order_id'], 'exist', 'targetClass' => Orders::className(), 'targetAttribute' => 'id', 'message' => 'Заказ не найден']
        ];
    }

    /** @inheritdoc */
    public function initData()
    {
        parent::initData();
        $this->order = Orders::find()->where(['status' => Orders::STATUS_NEW])->andWhere(['user_id' => $this->rest->user->id])->andWhere(['id' => $this->order_id])->one();
        if (!$this->order) {
            \Yii::$app->response->throwError(ErrorCode::NOT_FOUND, "Заказ не найден");
        }
    }

    /** @inheritdoc */
    public function save(){
        $data = [];
        $module = $this->order->api->getModule();
        if ($this->rest->user->can("api_user")){
            $module->buyOrder($this->order);
            $data['police_url'] = $this->order->getPoliceLink();
        } else {
            $this->order->status = Orders::STATUS_PAYED;
            $this->order->info = [
                'message' => 'Заказ данного полиса осуществлен в тестовом режиме',
                'mode'    => 'test'
            ];
            $data['police_url'] = '#';
            $data['message'] = 'Оформление полиса в тестовом режиме аккаунта эмулируется.';
        }
        $this->order->user_id = $this->rest->user->id;
        $this->order->save();

        $data['price'] = $this->order->price;
        $data['currency_id'] = $this->order->currency_id;

        return $data;
    }
}