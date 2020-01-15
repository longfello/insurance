<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiRgs\models\Classifier */
/* @var $id integer */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="classifier-form">
    <?php
    $form = ActiveForm::begin();
    echo $form->errorSummary($model);
    echo $form->field($model, 'ext_id')->textInput();
    echo $form->field($model, 'title')->textInput();
    echo $form->field($model, 'class')->textInput();
    ?>
    <div class="form-group">
        <?php echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>