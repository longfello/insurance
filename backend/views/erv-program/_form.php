<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiErv\models\Program */
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
	      <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	      <?= $form->field($model, 'product_code')->textInput(['maxlength' => true]) ?>
	      <?= $form->field($model, 'tariff_code')->textInput(['maxlength' => true]) ?>
	      <?= $form->field($model, 'price')->widget(\kartik\widgets\TouchSpin::class, [
	        'options' => [
		        'autocomplete' => 'off',
	        ],
          'pluginOptions' => [
	          'min' => 0,
	          'max' => 10000000,
	          'step' => 0.01,
	          'decimals' => 2,
            'verticalbuttons' => true
          ],
        ]);
        ?>
        <?= $form->field($model, 'price_type')->dropDownList([
          'day' => 'за день',
          'year' => 'за год'
        ]) ?>
	      <?= $form->field($model, 'summa')->widget(\kartik\widgets\TouchSpin::class, [
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
	      <?= $form->field($model, 'region_id')->dropDownList(\yii\helpers\ArrayHelper::map(
		      \common\modules\ApiErv\models\Regions::find()->all(),
		      'id',
		      'name'
	      )) ?>
          <?= $form->field($model, 'tariff_code_sport')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'tariff_code_cancel')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'tariff_code_cancel_p')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'pregnant_week')->dropDownList(\yii\helpers\ArrayHelper::map(
              $model->getPregnantVariants(),
              'id',
              'name'
          )) ?>
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
            $p2r     = \common\modules\ApiErv\models\Program2Risk::findOne(['risk_id' => $risk->id, 'program_id' => $model->id]);
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
  	                $ervRisk = \common\modules\ApiErv\models\Risk::findOne(['parent_id' => $risk->id]);
                    $name = $ervRisk?"<b title='Из справочника рисков ERV'>{$ervRisk->description}</b><br>":"";
                    $name .= '<span title="Из внутреннего справочника">('.$risk->name;
                    $name .= $risk->category?"- {$risk->category->name}":"";
                    $name .= ")</span>";
	                ?>
                  <?= $name ?>
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


