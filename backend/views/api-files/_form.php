<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ApiFiles */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="api-files-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model) ?>
    <?= $form->field($model, 'api_id', ['template' => '{input}'])->hiddenInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'file')->widget(
      \trntv\filekit\widget\Upload::className(),
      [
        'url' => ['/file-storage/upload'],
        'maxFileSize' => 5000000, // 5 MiB
      ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
