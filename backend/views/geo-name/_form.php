<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\geo\models\GeoName */
/* @var $form \common\components\MLActiveForm */
?>

<div class="geo-name-form">

    <?php $form = \common\components\MLActiveForm::begin(); ?>

    <?= $form->textFieldGroup($model, 'name') ?>

    <?= $form->field($model, 'population')->textInput() ?>

    <?= $form->field($model, 'timezone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'zone_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\modules\geo\models\GeoZone::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name')) ?>

    <?= $form->field($model, 'country_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\modules\geo\models\GeoCountry::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name')) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'domain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'google_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php \common\components\MLActiveForm::end(); ?>

</div>
