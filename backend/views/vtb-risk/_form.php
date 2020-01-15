<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \common\modules\ApiVtb\models\Risk2internal;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Risk */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="risk-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'description')->textarea(['maxlength' => true])->hint('Если описание не заполнено, то описание будет взято из описаний внутренних рисков') ?>

    <?php echo $form->field($model, 'class')->textInput(['maxlength' => true]) ?>

    <?php if (!$model->isNewRecord) { ?>
        <h5>Соответствие внутреннему справочнику</h5>
        <?php
          $risks = \common\models\Risk::find()->orderBy(['name' => SORT_ASC])->all();
          foreach ($risks as $risk){
            /** @var $risk \common\models\Risk */
            ?>

            <div class="col-xs-6">
              <label for="risk-<?=$risk->id ?>">
                <input id="risk-<?=$risk->id ?>" name="Risk2internal[]" value="<?= $risk->id ?>" type="checkbox" <?php if (Risk2internal::findOne(['risk_id' => $model->id, 'internal_id' => $risk->id])){ echo('checked'); } ?>>
                <?=$risk->name ?>
              </label>
            </div>
            <?php
          }
        ?>
        <div class="clearfix"></div>
    <?php } ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
