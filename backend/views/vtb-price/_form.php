<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \yii\helpers\ArrayHelper;
use \common\modules\ApiVtb\models\Amount;
use \common\modules\ApiVtb\models\Period;
use \common\modules\ApiVtb\models\Regions;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Price */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="price-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'program_id', ['template' => '{input}'])->hiddenInput() ?>

    <?php echo $form->field($model, 'amount_id')->dropDownList(ArrayHelper::map(Amount::find()->all(),'id','amount')) ?>

    <?php echo $form->field($model, 'period_id')->dropDownList(ArrayHelper::map(Period::find()->all(),'id','asText')) ?>

    <?php echo $form->field($model, 'region_id')->dropDownList(ArrayHelper::map(Regions::find()->all(),'id','name')) ?>

    <?php echo $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
