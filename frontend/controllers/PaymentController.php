<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace frontend\controllers;

use common\models\Orders;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

/**
 * Class PaymentController Контроллер оплат
 * @package frontend\controllers
 */
class PaymentController extends Controller {

    /**
     * @inheritdoc
     * @param \yii\base\Action $action
     *
     * @return bool
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        return parent :: beforeAction($action);
    }

    /**
     * Callback платежной системы
     */
    public function actionPaymentIpn()
    {
        $this->enableCsrfValidation = false;
        $error = false;
        $hash = Yii::$app->request->post('HASH', '');

        //Calc hash
        $post_data = $_POST;
        $calc_hash = '';
        foreach ($post_data as $key=>$dataValue) {
            if ($key!="HASH") {
                if (is_array($dataValue)) $dataValue = $dataValue[0];
                $calc_hash .= strlen($dataValue) . $dataValue;
            }
        }
        $calc_hash = hash_hmac('md5', $calc_hash, Yii::$app->payu->secretKey);

        if ($calc_hash==$hash) {
            $order_status = Yii::$app->request->post('ORDERSTATUS', false);
            $pay_method = Yii::$app->request->post('PAYMETHOD', false);
            $order_id = Yii::$app->request->post('REFNOEXT', false);

            $order = Orders::findOne(['id' => $order_id]);
            if ($order) {
	            $work_status = (Yii::$app->payu->debug == 'true')?"TEST":"COMPLETE";
                if ($order_status==$work_status || ($order_status=='PAYMENT_AUTHORIZED' && $pay_method!='Visa/MasterCard/Eurocard')) {
                    if ($order->status == Orders::STATUS_NEW) {
                        $order->api->getModule()->buyOrder($order);
                    } else $error = "Заказ уже отмечен как оплаченный";
                }
                $result = Yii::$app->payu->handleIpnRequest();
                echo $result;
            } else $error = "Заказ не найден";
        } else $error = 'Неправильная подпись запроса';

        if ($error) echo $error;
    }
}