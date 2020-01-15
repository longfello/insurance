<?php
/**
 * Copyright (c) kvk-group 2018.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 03.01.18
 * Time: 17:26
 *
 * @var $this \yii\web\View
 * @var $order \common\models\Orders
 */
use \common\components\Calculator\forms\TravelForm;

$api = $order->api->getModule();
$order->calc_form->forceRemoteCalc = true;
$searcher = $api->getProgramSearch($order->calc_form);
if (!$searcher) {
    throw new HttpException(404);
}
$program = $searcher->adapt($order->program, \common\components\ApiModule::CALC_API);
?>


<form action="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/api/travel/calc-choose-program'], true) ?>" method="post">
    <input name="program" value="<?= base64_encode(serialize($program)) ?>" type="hidden">
    <input name="_csrf" value="<?= Yii::$app->request->csrfToken ?>" type="hidden">
    <button type="submit" class="button button_color_green button_size_m button_uppercase">Купить</button>
</form>

