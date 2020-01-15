<?php

use yii\helpers\Html as HtmlHelper;
use yii\bootstrap\Html as HtmlBootstrap;
use yii\widgets\ActiveForm;
use common\models\Risk;
use common\modules\ApiZetta\models\Risk2dict;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiZetta\models\Risk */
/* @var $form yii\widgets\ActiveForm */
/* @var $id integer */
?>

<div class="program-form">
    <?php
    $form = ActiveForm::begin();

    $risks = Risk::find()->orderBy(['name' => SORT_ASC])->all();
    foreach ($risks as $risk) {
        echo '<div>';
        echo HtmlBootstrap::checkbox('Risk[]', (bool) Risk2dict::find()->where(['risk_id' => $id, 'internal_id' => $risk->id])->count(), ['value' => $risk->id, 'id' => 'risk-' . $risk->id]);
        echo HtmlBootstrap::label($risk->name, 'risk-' . $risk->id);
        echo '</div>';
    }

    echo '<br /><div class = "form-group">';
    echo HtmlHelper::submitButton('Сохранить', ['class' => 'btn btn-primary']);
    echo '</div>';

    ActiveForm::end();
    ?>
</div>