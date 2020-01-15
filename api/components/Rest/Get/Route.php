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

use api\components\ErrorCode;
use api\components\Response;
use api\components\Rest\RestMethod;
use common\models\Orders;

/**
 * Class DictAdditionalConditions
 *
 * ### Роутер для внешних ссылок
 *
 * Тип запроса | URI | Комментарий
 * --- | --- | ---
 * GET | {%api_url}route | Переход по внешней ссылке
 *
 * @package api\components\Rest\Get
 */
class Route extends RestMethod
{
    /** @var Orders */
    public $order;

    /**
     * @inheritdoc
     */
    public $shareDocumentation = false;

    /**
     * @inheritdoc
     * @throws \Exception
     * @throws \yii\web\HttpException
     */
    public function init(){
        parent::init();
        \Yii::$app->response->format = Response::FORMAT_HTML;
        $token = \Yii::$app->request->get('token');

        $this->order = Orders::findOne(['slug' => $token]);
        if (!$this->order){
            \Yii::$app->response->throwError(ErrorCode::NOT_FOUND);
        }
    }

    /** @inheritdoc */
    public function run(){
        $view = 'error';
        switch ($this->order->status){
            case Orders::STATUS_NEW:
                $view = Orders::STATUS_NEW;
                break;
            case Orders::STATUS_CALC:
                $view = Orders::STATUS_CALC;
                break;
        }

        return $this->render($view, [
            'order' => $this->order
        ]);
    }

}