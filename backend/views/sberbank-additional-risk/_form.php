<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiSberbank\models\AdditionalRisk */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="risk-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'paragraph')->textInput(['maxlength' => true]) ?>


    <div class="panel panel-default">
        <div class="panel-heading">Территории</div>
        <div class="panel-body">
            <?php foreach(\common\modules\ApiSberbank\models\Territory::find()->orderBy(['id' => SORT_ASC])->all() as $territory) { ?>
                <div class="col-xs-4">
                    <?= \yii\bootstrap\Html::checkbox('territory[]', (bool)\common\modules\ApiSberbank\models\AdditionalRisk2Territory::find()->where(['territory_id' => $territory->id, 'risk_id' => $model->id])->count(), ['value' => $territory->id, 'id' => 'territory-'.$territory->id]); ?>
                    <?= \yii\bootstrap\Html::label($territory->name, 'territory-'.$territory->id); ?>
                </div>
            <?php } ?>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">Внутренние риски</div>
        <div class="panel-body">
            <?php foreach(\common\models\Risk::find()->orderBy(['name' => SORT_ASC])->all() as $one) { ?>
                <div class="col-xs-12">
                    <?= \yii\bootstrap\Html::checkbox('risk[]', (bool)\common\modules\ApiSberbank\models\AdditionalRisk2internal::find()->where(['internal_id' => $one->id, 'risk_id' => $model->id])->count(), ['value' => $one->id, 'id' => 'risk-'.$one->id]); ?>
                    <?= \yii\bootstrap\Html::label($one->name, 'risk-'.$one->id); ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
