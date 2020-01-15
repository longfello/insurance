<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CostInterval */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cost-interval-form">

    <?php $form = ActiveForm::begin(); ?>

  	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

  	<?= $form->field($model, 'description')->textarea() ?>

    <?= $form->field($model, 'from')->textInput() ?>

    <?= $form->field($model, 'to')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
