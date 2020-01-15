<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\AdditionalCondition */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="additional-condition-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

  <?php if (!$model->isNewRecord) { ?>
    <?php echo $form->field($model, 'params')->widget(
      trntv\aceeditor\AceEditor::className(),
      [
        'mode' => 'json'
      ]
    )->label($model->params_description) ?>
  <?php } ?>

	<?php if ($model->isNewRecord) { ?>
		<?php echo $form->field($model, 'params_description')->textInput(['maxlength' => true]) ?>
  <?php } ?>

    <?php echo $form->field($model, 'class')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
