<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\ApiSberbank\models\Territory;
use common\modules\ApiSberbank\models\Territory2Program;
use common\models\CostInterval;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiSberbank\models\Program */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="program-form">
	<?php $form = ActiveForm::begin(); ?>
    <ul class="nav nav-tabs">
      <li class="active"><a  href="#tab1" data-toggle="tab">Основная информация</a></li>
      <li><a href="#tab2" data-toggle="tab">Риски</a></li>
    </ul>

    <div class="tab-content ">
      <div class="tab-pane active" id="tab1">
	      <?= $form->field($model, 'insProgram')->textInput(['maxlength' => true]) ?>
	      <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
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

          <div class="panel panel-default">
              <div class="panel-heading">Страховая сумма</div>
              <div class="panel-body">
                  <?php foreach(CostInterval::find()->orderBy(['id' => SORT_ASC])->all() as $interval) { ?>
                      <div class="col-xs-4">
                          <?= \yii\bootstrap\Html::radio('Program[cost_interval_id]', $model->cost_interval_id==$interval->id, ['value' => $interval->id, 'id' => 'interval-'.$interval->id]); ?>
                          <?= \yii\bootstrap\Html::label($interval->name, 'interval-'.$interval->id); ?>
                      </div>
                  <?php } ?>
              </div>
          </div>
          <div class="panel panel-default">
              <div class="panel-heading">Территории</div>
              <div class="panel-body">
                  <?php foreach(Territory::find()->orderBy(['id' => SORT_ASC])->all() as $territory) { ?>
                      <div class="col-xs-4">
                          <?= \yii\bootstrap\Html::checkbox('territory[]', (bool)(bool)Territory2Program::find()->where(['territory_id' => $territory->id, 'program_id' => $model->id])->count(), ['value' => $territory->id, 'id' => 'territory-'.$territory->id]); ?>
                          <?= \yii\bootstrap\Html::label($territory->name, 'territory-'.$territory->id); ?>
                      </div>
                  <?php } ?>
              </div>
          </div>
      </div>
      <div class="tab-pane" id="tab2">

        <div class="container-fluid">
          <div class="col-xs-1">Вкл.</div>
          <div class="col-xs-1">Опциональный</div>
          <div class="col-xs-1">Страховая сумма</div>
          <div class="col-xs-9">Риск</div>
        </div>

        <?php
          $risks = \common\models\Risk::find()->orderBy(['category_id' => SORT_ASC, 'name' => SORT_ASC])->all();
          foreach($risks as $risk) {
            $p2r     = \common\modules\ApiSberbank\models\Program2Risk::findOne(['risk_id' => $risk->id, 'program_id' => $model->id]);
            $checked = $p2r?"checked='checked'":"";
              ?>
              <div class="container-fluid">
                <div class="col-xs-1"><input <?= $checked ?> type="checkbox" name="risk[]" value="<?= $risk->id ?>"></div>
                <div class="col-xs-1"><input <?= ($p2r && $p2r->is_optional)?"checked='checked'":"" ?> type="checkbox" name="is_optional_<?= $risk->id ?>" value="1"></div>
                <div class="col-xs-1">
	                <?=
                  \kartik\widgets\TouchSpin::widget([
		                'name' => 'price_for_'.$risk->id,
		                'value' => $p2r?$p2r->summa:0,
  	                'options' => [
                      'autocomplete' => 'off'
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
                </div>
                <div class="col-xs-9">
	                <?php
                    echo '<span title="Из внутреннего справочника"><b>'.$risk->name.'</b>';
                    echo $risk->category?" - {$risk->category->name}":"";
                    echo "</span><br/>";
                    echo "<input type='text' name='name_for_".$risk->id."' value='".(($p2r)?$p2r->name:'')."' placeholder='Выводимое название (если пустое - выводится из общего справочника)' class='form-control'/>"
	                ?>
                </div>
              </div>
              <hr/>
            <?php
          }
        ?>
      </div>
    </div>
    <div class="form-group">
      <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
	<?php ActiveForm::end(); ?>
</div>


