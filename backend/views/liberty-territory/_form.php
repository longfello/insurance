<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiLiberty\models\Territory */
/* @var $form yii\widgets\ActiveForm */
/* @var $id integer */
?>

<div class="program-form">
	<?php $form = ActiveForm::begin(); ?>

  	<?= $form->field($model, 'enabled')->dropDownList([
	    0 => 'Запрещена',
	    1 => 'Разрешена'
    ]) ?>

    <?php foreach(\common\models\GeoCountry::find()->orderBy(['name' => SORT_ASC])->all() as $country) { ?>
        <div class="col-xs-4">
          <?= \yii\bootstrap\Html::checkbox('GeoCountry[]', (bool)(bool)\common\modules\ApiLiberty\models\Territory2Dict::find()->where(['id_area' => $id, 'internal_id' => $country->id])->count(), ['value' => $country->id, 'id' => 'country-'.$country->id]); ?>
          <?= \yii\bootstrap\Html::label($country->name, 'country-'.$country->id); ?>
        </div>
    <?php } ?>

    <div class="form-group">
      <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
	<?php ActiveForm::end(); ?>
</div>


