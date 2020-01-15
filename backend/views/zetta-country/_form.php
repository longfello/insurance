<?php

use yii\helpers\Html as HtmlHelper;
use yii\bootstrap\Html as HtmlBootstrap;
use yii\widgets\ActiveForm;
use common\models\GeoCountry;
use common\modules\ApiZetta\models\Country2dict;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiZetta\models\Country */
/* @var $form yii\widgets\ActiveForm */
/* @var $id integer */
?>

<div class="program-form">
    <?php
    $form = ActiveForm::begin();

    echo $form->field($model, 'enabled')->dropDownList([
        0 => 'Запрещена',
        1 => 'Разрешена'
    ]);

    $geoCountries = GeoCountry::find()->orderBy(['name' => SORT_ASC])->all();
    foreach ($geoCountries as $country) {
        echo '<div class="col-xs-4">';
        echo HtmlBootstrap::checkbox('GeoCountry[]', (bool) Country2dict::find()->where(['country_id' => $id, 'internal_id' => $country->id])->count(), ['value' => $country->id, 'id' => 'country-' . $country->id]);
        echo HtmlBootstrap::label($country->name, 'country-' . $country->id);
        echo '</div>';
    }

    echo '<div class = "form-group">';
    echo HtmlHelper::submitButton('Сохранить', ['class' => 'btn btn-primary']);
    echo '</div>';

    ActiveForm::end();
    ?>
</div>