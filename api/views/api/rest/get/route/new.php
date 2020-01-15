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
?>

<?php $this->beginContent('@api/views/layouts/api.php') ?>

<h1>Происходит переадресация</h1>
<p>Сейчас вы будете переадресованы...</p>
<p>Если этого не происходит, нажмите <a class="btn btn-default" href="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl('site/index', true) ?>">эту кнопку</a> для перехода на главную страницу сайта</p>

<?php $short_class_name = \yii\helpers\StringHelper::basename(get_class($order->calc_form)); ?>

<div class="hidden33 js-calculator-form">
    <?= $this->render('_new_form/'.$short_class_name, ['order' => $order]); ?>
</div>

<?php $this->endContent() ?>
