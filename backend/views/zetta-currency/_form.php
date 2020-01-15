<?php

use yii\helpers\Html as HtmlHelper;
use yii\bootstrap\Html as HtmlBootstrap;
use yii\widgets\ActiveForm;
use common\models\Currency;
use common\modules\ApiZetta\models\Currency2dict;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiZetta\models\Currency */
/* @var $form yii\widgets\ActiveForm */
/* @var $id integer */
?>

<div class="currency-form">
    <?php
    $form = ActiveForm::begin();

    echo $form->field($model, 'enabled')->dropDownList([
        0 => 'Запрещена',
        1 => 'Разрешена'
    ]);

    $currencies = Currency::find()->orderBy(['name' => SORT_ASC])->all();
    foreach ($currencies as $currency) {
        echo '<div class="col-xs-4">';
        echo HtmlBootstrap::checkbox('CommonCurrency[]', (bool) Currency2dict::find()->where(['currency_id' => $id, 'internal_id' => $currency->id])->count(), ['value' => $currency->id, 'id' => 'currency-' . $currency->id]);
        echo HtmlBootstrap::label($currency->name, 'currency-' . $currency->id);
        echo '</div>';
    }

    echo '<div class = "form-group">';
    echo HtmlHelper::submitButton('Сохранить', ['class' => 'btn btn-primary']);
    echo '</div>';

    ActiveForm::end();
    ?>
</div>