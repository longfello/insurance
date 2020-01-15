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

$layout = TravelForm::SCENARIO_HOME;
switch ($order->status){
    case \common\models\Orders::STATUS_NEW:
        $layout = TravelForm::SCENARIO_CALC;
        break;
}

$type = \common\models\InsuranceType::findOne(['slug' => \common\components\Calculator\forms\TravelForm::SLUG_TRAVEL]);
$action = Yii::$app->urlManagerFrontend->createAbsoluteUrl(['page/view', 'slug' => $type->resultPage->slug], true);

?>

<?= \common\components\Calculator\widgets\travel\CalculatorWidget::widget(['model' => $order->calc_form, 'layout' => $layout]); ?>

<?php
$this->registerJs("
    $('.js-calculator-form form').attr('action', \"{$action}\");
    $('.js-calculator-form form').submit();
");
?>
