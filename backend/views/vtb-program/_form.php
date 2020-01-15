<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Program */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="program-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'rule')->widget(
      \trntv\filekit\widget\Upload::className(),
      [
        'url' => ['/file-storage/upload'],
        'maxFileSize' => 5000000, // 5 MiB
      ]);
    ?>
    <?php echo $form->field($model, 'police')->widget(
      \trntv\filekit\widget\Upload::className(),
      [
        'url' => ['/file-storage/upload'],
        'maxFileSize' => 5000000, // 5 MiB
      ]);
    ?>

    <?= $form->field($model, 'pregnant_week')->dropDownList(\yii\helpers\ArrayHelper::map(
        $model->getPregnantVariants(),
        'id',
        'name'
    )) ?>
    <?= $form->field($model, 'baggage_sum')->dropDownList(['500'=>500,'1000'=>1000,'1500'=>1500,'2000'=>2000]); ?>
    <div class="form-group">
        <?php echo Html::submitButton('Добавить' , ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
