<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\components\Calculator\models\travel\FilterSolution */
/* @var $params array */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="filter-solution-form">

    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">Основная информация</a></li>
        <li><a href="#tab2" data-toggle="tab">Параметры</a></li>
        <li><a href="#tab3" data-toggle="tab">Api</a></li>
        <li><a href="#tab4" data-toggle="tab">Страны</a></li>
    </ul>
    <br/>
    <?php
    $form = ActiveForm::begin();
    ?>
    <div class="tab-content row">
        <div class="tab-pane active col-xs-12" id="tab1">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'description')->textarea(); ?>
            <?= $form->field($model, 'is_front')->checkbox() ?>
            <?= $form->field($model, 'is_api')->checkbox() ?>
            <?= $form->field($model, 'thumbnail')->widget(
                \trntv\filekit\widget\Upload::className(),
                [
                    'url' => ['/file-storage/upload'],
                    'maxFileSize' => 5000000, // 5 MiB
                ]);
            ?>
        </div>
        <div class="tab-pane col-xs-12" id="tab2">
            <?php
            $traver_model = new \common\components\Calculator\forms\TravelForm();
            ?>

            <div class="checkbox-list checkbox-list_additional additional-options__progress additional-options__progress_sum">
                <?php
                $filters = \common\components\Calculator\models\travel\FilterParam::find()->where(['position' => \common\components\Calculator\models\travel\FilterParam::POSITION_EXTENDED])->orderBy(['sort_order' => SORT_ASC])->all();
                foreach($filters as $filter){
                    /** @var $filter \common\components\Calculator\models\travel\FilterParam */
                    if ($filter && $handler = $filter->getHandler()) {
                        $handler->load($params);
                        echo $handler->render($form, $traver_model);
                    }
                }
                ?>
            </div>
            <div class="checkbox-list">
                <?php
                $filters = \common\components\Calculator\models\travel\FilterParam::find()->where(['position' => \common\components\Calculator\models\travel\FilterParam::POSITION_MEDICAL])->orderBy(['sort_order' => SORT_ASC])->all();
                foreach($filters as $filter){
                    /** @var $filter \common\components\Calculator\models\travel\FilterParam */
                    if ($filter && $handler = $filter->getHandler()) {
                        $handler->load($params);
                        echo $handler->render($form, $traver_model);
                    }
                }
                ?>
            </div>
            <div class="dropdown">
                <div class="dropdown__header">
                    <div class="dropdown__title title title_size_l"><h3>Дополнительные риски</h3></div>
                </div>
                <div class="dropdown__content">
                    <div class="checkbox-list">
                        <?php
                        $filters = \common\components\Calculator\models\travel\FilterParam::find()->where(['position' => \common\components\Calculator\models\travel\FilterParam::POSITION_ADDITIONAL])->orderBy(['sort_order' => SORT_ASC])->all();
                        foreach($filters as $filter){
                            /** @var $filter \common\components\Calculator\models\travel\FilterParam */
                            if ($filter && $handler = $filter->getHandler()) {
                                $handler->load($params);
                                echo $handler->render($form, $traver_model);
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="tab3">
            <?php foreach(\common\models\Api::find()->orderBy(['name' => SORT_ASC])->all() as $one_api) { ?>
                <div class="col-xs-2">
                    <?= \yii\bootstrap\Html::checkbox('Api[]', (bool)(bool)\common\components\Calculator\models\travel\FilterSolution2api::find()->where(['filter_solution_id' => $model->id, 'api_id' => $one_api->id])->count(), ['value' => $one_api->id, 'id' => 'api-'.$one_api->id]); ?>
                    <?= \yii\bootstrap\Html::label($one_api->name, 'api-'.$one_api->id); ?>
                </div>
            <?php } ?>
        </div>
        <div class="tab-pane" id="tab4">
            <?php foreach(\common\models\GeoCountry::find()->orderBy(['name' => SORT_ASC])->all() as $country) { ?>
                <div class="col-xs-4">
                    <?= \yii\bootstrap\Html::checkbox('GeoCountry[]', (bool)(bool)\common\components\Calculator\models\travel\FilterSolution2country::find()->where(['filter_solution_id' => $model->id, 'country_id' => $country->id])->count(), ['value' => $country->id, 'id' => 'country-'.$country->id]); ?>
                    <?= \yii\bootstrap\Html::label($country->name, 'country-'.$country->id); ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
frontend\assets\RangesliderAsset::register($this);
$this->registerCss("
#tab2 .checkbox-list__item {
    padding-left: 20px;
}

.additional-options__progress_sum .additional-options__label {
    font-weight: bold;
}

.helper__answer, .helper__text, .euro-sign, .example-coast {
  display: none;
}

.cancel-progress.escape-travel label.checkbox{
    font-weight: normal;
}

.progress__range {
  padding-left: 40px;
  padding-right: 30px;
  padding-bottom: 50px;
  padding-top: 50px;
  font-family: 'Open Sans', Helvetica, Arial, sans-serif;
  font-size: 14px;
}

.additional-options__progress_sum .progress__range, .pregnant-progress {
    width: 25%;
    padding-right: 0;
}

.progress__checkbox-help {
  position: relative;
  padding-right: 64px;
}
.progress__icon-help {
  position: absolute;
  top: 50%;
  margin-left: 13px;
  transform: translateY(-50%);
}
.irs-line {
  height: 4px;
  background-color: #e0e4ea;
  z-index: 1;
}
.irs-single {
  top: -37px;
  font-family: 'Open Sans', Helvetica, Arial, sans-serif;
  font-size: 14px;
  line-height: 26px;
  text-align: center;
  padding-right: 10px;
  padding-left: 10px;
  background-color: #fff;
  border-radius: 3px;
  box-shadow: 0 2px 5px #e0e4ea;
  z-index: 9999;
}
.irs-single:after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: calc(50% - 5px);
  width: 0;
  height: 0;
  border-style: solid;
  border-width: 5px 5px 0 5px;
  border-color: #fff transparent transparent transparent;
}
.irs-slider.single {
  top: -5px;
  width: 15px;
  height: 15px;
  border-radius: 50%;
  background-color: #77bc1f;
}
.irs-grid-pol,
.irs-min,
.irs-max {
  display: none;
}

.irs-grid-pol:not(.small) {
    display: block;
    position: absolute;
    top: 10px;
    margin-left: -7.5px;
    width: 15px;
    height: 15px;
    font-size: 0;
    border-radius: 50%;
    background-color: #e0e4ea;
    cursor: pointer;
}

.irs-grid-text {
    color: #7c7c7c;
    bottom: 20px;
    font-size: 10px;
}
 ");

$this->registerJs("
  $(document).ready(function(){

      $(\".example-coast__item\").on('click', function () {
          var v = $(this).find('.example-coast__value').text();
          $(\".input_escape_sum\").val(v);
      });

      $('.input_escape_sum').on('keydown', function() {
          if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||  // Разрешаем: backspace, delete, tab и escape
              (event.keyCode == 65 && event.ctrlKey === true) ||   // Разрешаем: Ctrl+A
              (event.keyCode >= 35 && event.keyCode <= 39)) {     // Разрешаем: home, end, влево, вправо
              return;// Ничего не делаем
          }
          else {
              // Убеждаемся, что это цифра, и останавливаем событие keypress
              if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                  event.preventDefault();
              }
          }
      });

      $('.input_escape_sum').on('keyup', function() {

          var val = $(this).val();
          if (val>5000) {
              $(this).parent().addClass('error');
              $('.escape_sum-block').append(\"<span class='escape_sum-block__error'>Максимальная сумма на одного застрахованного - 5000 Евро</span>\");
              $(this).val(5000);
          } else {
            $('.escape_sum-block__error').remove();
            $(this).parent().removeClass('error');
          }
      });
      
      var variants =$('.pregnant-progress .js-progress').data('variants');
      variants = variants?variants.split(','):[];

        $('.pregnant-progress .js-progress').each(function(){
              $(this).ionRangeSlider({
                type: 'single',
                from: variants.indexOf($(this).val()),
                keyboard: true,
                values: variants,
                grid: true,
                prettify: function(num) {
                  return 'до ' + num;
                }
            });
        });

  var variants = $('.period__progress .js-progress').data('variants');
  variants = variants?variants.split(','):[];

  $('.period__progress .js-progress').each(function(){
    $(this).ionRangeSlider({
      type: 'single',
      from: variants.indexOf($(this).val()),
      keyboard: true,
      values: variants,
      grid: true,
      prettify: function(num) {
        return num;
      }
    });

  });
  variants =$('.additional-options__progress_sum .js-progress').data('variants');
  variants = variants?variants.split(','):[];
  $('.additional-options__progress_sum').find('.js-progress').each(function(){
      $(this).ionRangeSlider({
        type: 'single',
  //    min: 0,
  //    max: 5,
  //    step: 1,
        from: variants.indexOf($(this).val()),
        keyboard: true,
        values: variants,
        grid: true
      });
    });
  
  });
");
