<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiTinkoff\models\Area */
/* @var $form yii\widgets\ActiveForm */
/* @var $id integer */
?>

<div class="program-form">
	<?php $form = ActiveForm::begin(); ?>

  	<?= $form->field($model, 'enabled')->dropDownList([
	    0 => 'Запрещен',
	    1 => 'Разрешен'
    ]) ?>

    <?php foreach(\common\models\GeoCountry::find()->where(['type'=>'territory'])->orderBy(['name' => SORT_ASC])->all() as $country) { ?>
        <div class="col-xs-4">
          <?= \yii\bootstrap\Html::checkbox('GeoCountry[]', (bool)(bool)\common\modules\ApiTinkoff\models\Area2Dict::find()->where(['area_id' => $id, 'internal_id' => $country->id])->count(), ['value' => $country->id, 'id' => 'country-'.$country->id]); ?>
          <?= \yii\bootstrap\Html::label($country->name, 'country-'.$country->id); ?>
        </div>
    <?php } ?>

    <div class="form-group">
      <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
	<?php ActiveForm::end(); ?>
</div>


