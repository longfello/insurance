<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \yii\helpers\ArrayHelper;
use common\modules\ApiAlphaStrah\models\Regions;
use common\modules\ApiAlphaStrah\models\Amount;
use common\components\Calculator\models\travel\FilterParam;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiAlphaStrah\models\Price */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="price-form">
	<?php $form = ActiveForm::begin(); ?>

  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab1" data-toggle="tab">Основная информация</a></li>
    <li><a href="#tab2" data-toggle="tab">В программу включено</a></li>
  </ul>

  <div class="tab-content ">
    <div class="tab-pane active" id="tab1">
		  <?php echo $form->errorSummary( $model ); ?>
  	  <?= $this->render('__form', ['model' => $model, 'form' => $form]); ?>

    </div>
    <div class="tab-pane" id="tab2">
      <br>
      <div class="form-group field-price-incs">
        <div class="container-fluid">
          <div class="col-xs-1"></div>
          <div class="col-xs-2">Страховая сумма</div>
          <div class="col-xs-5">Риск</div>
          <div class="col-xs-4">Параметр фильтра <a class="btn btn-success pull-right js-add-risk" href="#">Добавить риск</a></div>
        </div>

        <div class="container-fluid risks-wrapper">
        <?php
        $incs = \common\modules\ApiAlphaStrah\models\PriceInc::find()->where(['price_id' => $model->id])->orderBy( [ 'name' => SORT_ASC ] )->all();
        foreach ( $incs as $inc ) {
          /** @var $inc \common\modules\ApiAlphaStrah\models\PriceInc */
          ?>
          <div class="col-wrapper">
              <div class="col-xs-1">
                <a class="js-remove-column btn btn-sm btn-danger">X</a>
              </div>
              <div class="col-xs-2">
                <input type="number" name="price_inc_amount[]" value="<?= $inc->amount ?>" class="form-control">
              </div>
              <div class="col-xs-5">
                  <?= Html::textInput('price_inc_name[]', $inc->name, ['class'=>"form-control"]) ?>
              </div>
              <div class="col-xs-4">
                <?= Html::dropDownList('price_inc_filter_id[]', $inc->filter_id, [null => ' '] +ArrayHelper::map(FilterParam::find()->where(['change_desc' => 1])->all(), 'id', 'name' )); ?>
              </div>
              <div class="clearfix"></div>
            </div>
          <?php
        }
        ?>
        </div>
      </div>
    </div>
  </div>

  <div class="form-group">
	  <?php echo Html::submitButton( $model->isNewRecord ? 'Добавить' : 'Сохранить', [ 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary' ] ) ?>
  </div>

	<?php ActiveForm::end(); ?>


  <div id="append_source" class="hidden">
    <div class="col-wrapper">
      <div class="col-xs-1"><a class="js-remove-column btn btn-sm btn-danger">X</a></div>
      <div class="col-xs-2"><input type="number" name="price_inc_amount[]"  class="form-control"></div>
      <div class="col-xs-5"><input type="text" name="price_inc_name[]" class="form-control"></div>
      <div class="col-xs-4"><?= Html::dropDownList('price_inc_filter_id[]', null, [null => ' '] + ArrayHelper::map(FilterParam::find()->where(['change_desc' => 1])->all(), 'id', 'name' )); ?></div>
      <div class="clearfix"></div>
    </div>
  </div>


  <?php $this->registerJs("
$(document).on('click', '.js-remove-column', function(e){
  e.preventDefault();
  $(this).parents('.col-wrapper').remove();
});
$('.js-add-risk').on('click', function(e){
  e.preventDefault();
  $('.risks-wrapper').append($('#append_source').html());
});
") ?>
</div>
