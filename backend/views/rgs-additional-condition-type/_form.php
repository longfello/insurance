<?php

use yii\helpers\Html as HtmlHelper;
use yii\bootstrap\Html as HtmlBootstrap;
use yii\widgets\ActiveForm;
use common\models\Risk;
use common\modules\ApiRgs\models\AdditionalConditionTypeRisk;


/* @var $this yii\web\View */
/* @var $model \common\modules\ApiRgs\models\AdditionalConditionType */
/* @var $form yii\widgets\ActiveForm */
/* @var $id integer */
?>

<div class="additional-condition-type-form">
    <?php
    $form = ActiveForm::begin();

    $risks = Risk::find()->orderBy(['name' => SORT_ASC])->all();
    foreach ($risks as $risk) {
        echo '<div class="col-xs-6">';
        echo HtmlBootstrap::checkbox('Risk[]', (bool) AdditionalConditionTypeRisk::find()->where(['additional_condition_type_id' => $id, 'risk_id' => $risk->id])->count(), ['value' => $risk->id, 'id' => 'risk-' . $risk->id]);
        echo HtmlBootstrap::label($risk->name, 'risk-' . $risk->id);
        echo '</div>';
    }

    echo '<div class = "form-group">';
    echo HtmlHelper::submitButton('Сохранить', ['class' => 'btn btn-primary']);
    echo '</div>';

    ActiveForm::end();
    ?>
</div>