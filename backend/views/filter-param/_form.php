<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\components\Calculator\models\travel\FilterParam */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="filter-param-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

  	<?= $form->field($model, 'type')->dropDownList([
  	  'checkbox' => 'Checkbox',
      'slider' => 'Slider',
      'checkbox+slider' => 'Checkbox+slider',
    ], ['prompt' => '']) ?>

    <?= $form->field($model, 'risk_id')->dropDownList([null => ' '] + \yii\helpers\ArrayHelper::map(
	    \common\models\Risk::find()->all(),
	    'id',
	    'name'
    )) ?>

    <?php if ($handler = $model->getHandler()){ ?>
      <?= $handler->getVariantsEditor($form) ?>
    <?php } ?>




    <?= $form->field($model, 'sort_order')->widget(\kartik\widgets\TouchSpin::class, [
      'options' => [
        'autocomplete' => 'off',
      ],
      'pluginOptions' => [
        'min' => 0,
        'max' => 10000000,
        'step' => 1,
        'decimals' => 0,
        'verticalbuttons' => true
      ],
    ]);
    ?>

  	<?= $form->field($model, 'position')->dropDownList([
  	  \common\components\Calculator\models\travel\FilterParam::POSITION_EXTENDED   => 'Расширенный',
  	  \common\components\Calculator\models\travel\FilterParam::POSITION_ADDITIONAL => 'Дополнительный',
  	  \common\components\Calculator\models\travel\FilterParam::POSITION_MEDICAL    => 'Медицинский',
    ]) ?>

  	<?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'change_desc')->checkbox() ?>

  <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
