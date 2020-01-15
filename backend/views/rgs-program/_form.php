<?php

use yii\helpers\Html as HtmlHelper;
use yii\bootstrap\Html as HtmlBootstrap;
use yii\widgets\ActiveForm;

use common\models\Risk;
use common\modules\ApiRgs\models\ProgramRisk;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiRgs\models\Program */
/* @var $form yii\widgets\ActiveForm */
/* @var $id integer */
?>

<div class="program-form">
    <?php
    $form = ActiveForm::begin();

    echo $form->field($model, 'enabled')->dropDownList([
        0 => 'Нет',
        1 => 'Да'
    ]);

    $risks = Risk::find()->orderBy(['name' => SORT_ASC])->all();
    foreach ($risks as $risk) {
        echo '<div class="col-xs-6">';
        echo HtmlBootstrap::checkbox('Risk[]', (bool) ProgramRisk::find()->where(['program_id' => $id, 'risk_id' => $risk->id])->count(), ['value' => $risk->id, 'id' => 'risk-' . $risk->id]);
        echo HtmlBootstrap::label($risk->name, 'risk-' . $risk->id);
        echo '</div>';
    }

    echo '<div class = "form-group">';
    echo HtmlHelper::submitButton('Сохранить', ['class' => 'btn btn-primary']);
    echo '</div>';

    ActiveForm::end();
    ?>
</div>