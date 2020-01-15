<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\InsuranceType */
/* @var $form \common\components\MLActiveForm */
?>

<div class="insurance-type-form">

    <?php $form = \common\components\MLActiveForm::begin(); ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->textFieldGroup($model, 'name') ?>

    <?= $form->textAreaGroup($model, 'description') ?>

    <?= $form->field($model, 'calc_page_id')->dropDownList([null => ' - '] + \yii\helpers\ArrayHelper::map(
	    \common\models\Page::find()->all(),
	    'id',
	    'title'
    )) ?>

    <?= $form->field($model, 'result_page_id')->dropDownList([null => ' - '] + \yii\helpers\ArrayHelper::map(
	    \common\models\Page::find()->all(),
	    'id',
	    'title'
    )) ?>

    <?= $form->field($model, 'program_page_id')->dropDownList([null => ' - '] + \yii\helpers\ArrayHelper::map(
	    \common\models\Page::find()->all(),
	    'id',
	    'title'
    )) ?>

    <?= $form->field($model, 'about_page_id')->dropDownList([null => ' - '] + \yii\helpers\ArrayHelper::map(
	    \common\models\Page::find()->all(),
	    'id',
	    'title'
    )) ?>

    <?= $form->field($model, 'landing_page_id')->dropDownList([null => ' - '] + \yii\helpers\ArrayHelper::map(
            \common\models\Landing::find()->all(),
            'id',
            'title'
        )) ?>

    <?= $form->field($model, 'sort_order')->widget(\kartik\widgets\TouchSpin::className(), [
	    'pluginOptions' => [
	      'min' => 0,
	      'max' => 100,
	      'step' => 1,
		    'verticalbuttons' => true,
		    'verticalupclass' => 'glyphicon glyphicon-plus',
		    'verticaldownclass' => 'glyphicon glyphicon-minus',
	    ]
    ]) ?>

    <?= $form->field($model, 'enabled')->dropDownList(['0'=>'Запрещен','1'=>'Разрешен']) ?>

    <?= $form->field($model, 'active')->dropDownList(['0'=>'Нет','1'=>'Да']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php \common\components\MLActiveForm::end(); ?>

</div>
