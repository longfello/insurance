<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GeoCountry */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="geo-country-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'iso_alpha2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'iso_alpha3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'iso_numeric')->textInput() ?>

    <?= $form->field($model, 'fips_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'capital')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'areainsqkm')->textInput() ?>

    <?= $form->field($model, 'population')->textInput() ?>

    <?= $form->field($model, 'continent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tld')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'currencyName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'postalCodeFormat')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'postalCodeRegex')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'languages')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'neighbours')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList($model->types) ?>

    <?= $form->field($model, 'shengen')->checkbox() ?>

    <?php
      if ($model->type == \common\models\GeoCountry::TYPE_TERRITORY) {
        echo $this->render('__sub-countries', ['model' => $model, 'form' => $form]);
      }
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
