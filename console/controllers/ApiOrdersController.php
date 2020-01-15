<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\Orders;

class ApiOrdersController extends Controller
{
    public function actionUpdatePayedStatus()
    {
        $orders = Orders::find()->where(['status'=>Orders::STATUS_PAYED])->all();
        foreach ($orders as $order) {
            /** Orders $order */
            echo ("\r\n Order id: ".$order->id);
            echo ("\r\n Api: ".$order->api->name);

            $api_module = $order->api->getModule();
            if ($api_module->confirmApiPayment($order)) {
                $log = $api_module->downloadOrder($order, null);
                $api_module->sendOrderMail($order);

                echo ("\r\n");
                var_dump($log);
            }
            echo ("\r\n".str_pad('',80, "=")."\r\n");
        }
    }

    public function actionDownloadPolices()
    {
        $orders = Orders::find()->where(['status'=>Orders::STATUS_PAYED_API, 'is_police_downloaded'=>0])->all();
        foreach ($orders as $order) {
            /** Orders $order */
            $api_module = $order->api->getModule();

            echo ("\r\n Order id: ".$order->id);
            echo ("\r\n Api: ".$order->api->name);
            if ($policy_url = $api_module->getPoliceLink($order)) {
                $order->is_police_downloaded = 1;
                if (!$order->save()) Yii::error($api_module->name." save order error".print_r($order->getErrors(), true));
            } else {
                $log = $api_module->downloadOrder($order, null);
                $api_module->sendOrderMail($order);
                echo("\r\n");
                var_dump($log);
            }
            echo ("\r\n".str_pad('',80, "=")."\r\n");
        }
    }
}