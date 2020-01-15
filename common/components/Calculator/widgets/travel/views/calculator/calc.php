<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\components\Calculator\forms\TravelForm
 *
 */

?>

<?php
  $form = \frontend\components\ActiveForm::begin([
	'action' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/api/'.\common\components\Calculator\forms\prototype::SLUG_TRAVEL.'/calc-prepare'], true),
    'form_type' => \common\components\Calculator\forms\prototype::SLUG_TRAVEL,
    'scenario' => 'calc',
    'enableClientScript' => false,
    'method' => 'post',
    'options' => [
        'class'  => 'insurance-find__body ajax-reloader',
        'data-target' => '.company-list__item_wrapper',
        'data-source' => '.company-list__item_wrapper',
    ]
  ]);
?>
<div class="tabs__content">
  <div data-content="fast-buy" class="_active js-content-item tabs__content-item">
    <!--<div class="fast-buy">
      <div class="fast-buy__options options">-->
        <div class="options__title title title_size_l">Данные о поездке</div>
        <div class="options__item">
          <div class="options__label">Страна
          </div>
          <div class="options__select">
	          <?= $form->field($model, 'countries')->dropDownList( \yii\helpers\ArrayHelper::map(
		          \common\models\GeoCountry::find()->orderBy(['name' => SORT_ASC])->all(),
		          'id',
		          'name'
	          ), [
		          'class' => "js-select select",
		          'style' => 'width:100%',
		          'multiple' => "multiple"
	          ]);?>
          </div>
        </div>
        <div class="options__item options__item_sep">
          <div class="options__label">Даты поездки<span class="days_count"></span>
          </div>
          <div class="date-field options__leaving">
	          <?= $form->field($model, 'dates')->textInput([
		          'class'        => "date-field__input input input_size_m js-datepicker",
	            'data-range'   => "true",
              'data-multiple-dates-separator' => " - ",
              'data-dates' => $model->datesAsJson(),
		          'autocomplete' => 'off',
              'readonly' => 'readonly'
	          ]); ?>
            <div class="date-field__icon">
              <svg class="icon icon_calendar ">
                <use xlink:href="#icon-calendar"></use>
              </svg>
            </div>
          </div>
        </div>
        <div class="options__item options__item_travelers_num">
          <div class="options__label">
            Количество путешественников:
          </div>
	        <?= $form->field($model, 'travellersCount', ['addon' => [
                    'prepend' => ['content'=>'<a href="#" class="change_travellersCount" data-kol="-1">-</a>'],
                    'append' => ['content' => '<a href="#" class="change_travellersCount" data-kol="1">+</a>']
                ]])->textInput([
		        'class'        => "input input_size_m input_travelers_num",
		        'autocomplete' => 'off'
	        ]); ?>
          <div class="clearfix"></div>
        </div>
        <div id="solutions_block_wrapper">
            <?php if (count($model->solutions)) { ?>
                <div class="solutions__title title title_size_l">Готовые решения</div>
                <div class="fast-buy__solutions solutions">
                    <div class="solutions__list">
                        <?php foreach ($model->solutions as $solution) {?>
                            <div class="js-rf-item solutions__item <?= ($model->solution==$solution->id)?'_active':''; ?>" <!--style="background-image:url(<?= $solution->thumbnail_base_url.'/'.$solution->thumbnail_path; ?>)"-->>
                                <div class="solutions__radio">
                                    <label class="radio">
                                        <input class="radio__input filter_solution_input" type="radio" name="filter_solution" value="<?= $solution->id; ?>" <?= ($model->solution==$solution->id)?'checked':''; ?>/><span class="radio__icon"></span>
                                    </label>
                                </div>
                                <div class="solutions__type">
                                    <div class="solutions__type-title title title_size_m"><?= $solution->name; ?></div>
                                    <div class="solutions__type-description"><?= $solution->description; ?></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <!--</div>-->
      <!--<div class="extended_options">-->
        <div class="solutions__title title title_size_l">Расширенный поиск</div>
        <div class="checkbox-list checkbox-list_additional additional-options__progress additional-options__progress_sum">

	        <?php
	        $filters = \common\components\Calculator\models\travel\FilterParam::find()->where(['position' => \common\components\Calculator\models\travel\FilterParam::POSITION_EXTENDED])->orderBy(['sort_order' => SORT_ASC])->all();
	        foreach($filters as $filter){
		        /** @var $filter \common\components\Calculator\models\travel\FilterParam */
		        if ($filter && $handler = $filter->getHandler()) {
			        $handler->load();
			        echo $handler->render($form, $model);
		        }
	        }
	        ?>
        </div>
      <!--</div>
      <div class="medical-options">-->
        <div class="solutions__title title title_size_l">
          <!--label class="checkbox checkbox_check_all">
            <span class="checkbox__label checkbox__label_check_all">Выбрать всё</span><input type="checkbox" class="checkbox__input checkbox__input_check_all"/><span class="checkbox__icon checkbox__icon_check_all"></span>
          </label-->
        </div>
        <div class="check_all"></div>
        <div class="checkbox-list">
          <?php
            $filters = \common\components\Calculator\models\travel\FilterParam::find()->where(['position' => \common\components\Calculator\models\travel\FilterParam::POSITION_MEDICAL])->orderBy(['sort_order' => SORT_ASC])->all();
            foreach($filters as $filter){
	            /** @var $filter \common\components\Calculator\models\travel\FilterParam */
	            if ($filter && $handler = $filter->getHandler()) {
	              $handler->load();
		            echo $handler->render($form, $model);
	            }
            }
          ?>
        </div>
      <!--</div>
    </div>-->
  </div>
</div>
<div class="dropdown">
  <div class="dropdown__header">
    <div class="dropdown__title title title_size_l">Дополнительные риски</div>
    <div class="dropdown__text">Показать</div>
    <div class="dropdown__icon">
      <svg class="icon icon_triangle-pointer ">
        <use xlink:href="#icon-triangle-pointer"></use>
      </svg>
    </div>
  </div>
  <div class="dropdown__content">
    <div class="checkbox-list">
	    <?php
	    $filters = \common\components\Calculator\models\travel\FilterParam::find()->where(['position' => \common\components\Calculator\models\travel\FilterParam::POSITION_ADDITIONAL])->orderBy(['sort_order' => SORT_ASC])->all();
	    foreach($filters as $filter){
		    /** @var $filter \common\components\Calculator\models\travel\FilterParam */
		    if ($filter && $handler = $filter->getHandler()) {
  		    $handler->load();
			    echo $handler->render($form, $model);
		    }
	    }
	    ?>
    </div>
  </div>
</div>
<input type="submit" class="hidden">
<?php \frontend\components\ActiveForm::end() ?>

<?php

$this->registerJs("
  var price_request = [];
  $(document).ready(function(){
      var loading_solution = false;

      $('form.insurance-find__body').find('input:not(.filter_solution_input), select').on('change', function(){
          $('body').trigger('change-filter');
          if(!loading_solution && $(this).attr('id')!='travelform-travellerscount' && $(this).attr('id')!='travelform-countries' && !$(this).hasClass('irs-hidden-input')) {
              $('#solutions_block_wrapper .solutions__item .filter_solution_input').prop('checked', false);
              $('#solutions_block_wrapper .solutions__item').removeClass('_active');
          }
      });

      $('form.insurance-find__body').find('#travelform-countries').on('change', function(){
          $.ajax({
                type: 'POST',
                url: '/api/travel/calc-update-solution-list.html',
                data:  $('form.insurance-find__body').serializeArray(),
                success: function(resp){
                    $('#solutions_block_wrapper').empty().append($(resp).find('#solutions_block_wrapper').html());
                }
          });
      });

      $('form.insurance-find__body').on('change', '#solutions_block_wrapper .solutions__item .filter_solution_input', function(){
        var solution_id = $('#solutions_block_wrapper .filter_solution_input:checked').val();

        window.reloader.showLoader('.company-list__item_wrapper');
        if (price_request && price_request!==undefined && price_request.length) {
           $.each(price_request, function( api_id, xhr ) {
            if (xhr!==undefined) xhr.abort();
           });
        }

        loading_solution = true;
        var input = $(this);
        if (typeof $(input).data('params') != 'undefined') {
            setSoultionParams($(input).data('params'));
        } else {
            $.ajax({
                type: 'POST',
                url: '/api/travel/calc-update-solution.html',
                data: {'solution_id':solution_id},
                success: function(params){
                   $(input).data('params', params);
                   setSoultionParams(params);
                },
                dataType: 'json'
            });
        }
        loading_solution = false;
      });

      $('body').off('change-filter').on('change-filter', function(e){
            if (price_request && price_request!==undefined && price_request.length) {
                $.each(price_request, function( api_id, xhr ) {
                    if (xhr!==undefined) xhr.abort();
                });
            }
            $('form.insurance-find__body').submit();
            console.log('1111');
      });

      var windowWidth = $(window).width();

          var helperW = $(\".helper\").outerWidth();
          var right = 0 - (helperW + parseInt($('.tabs__content-item').css('padding-right')) + helperW / 3);
          console.log(right);

          $('.helper__text').css({'width': helperW - 30, 'right': right});
          var rightHov = right + ((helperW / 3) + 10);

          $('.checkbox-list__item').mouseover(function () {
              $(this).find('.helper__text').css({'right': rightHov});
          });

          $('.checkbox-list__item').mouseout(function () {
              $(this).find('.helper__text').css({'right': right});
          });

          $(document).on(\"click\", \".js-company-more-btn\", function() {
              $(this).parents(\".js-company\").toggleClass(\"_open\");
              if(windowWidth > 480) {
                  resultsFixed();
              }
          });

      $(\".example-coast__item\").on('click', function () {
          var v = $(this).find('.example-coast__value').text();
          $(\".input_escape_sum\").val(v);
          $('body').trigger('change-filter');
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

      $('#travelform-travellerscount').on('keydown', function(e) {
        // Allow: backspace, delete, tab, escape, enter and
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
      });

      $('#travelform-travellerscount').on('blur', function() {
          $('.travellerscount-block__error').remove();
          $(this).parent().removeClass('error');

          var val = $(this).val();
          if (val <= 0) {
              $(this).parent().addClass('error');
              $('.field-travelform-travellerscount').append(\"<span class='travellerscount-block__error'>Минимум 1 человек</span>\");
              $(this).val(1);
          }
      });

      $('.change_travellersCount').on('click', function(e) {
        e.preventDefault();
        var change = parseInt($(this).data('kol'));
        var cur = parseInt($('#travelform-travellerscount').val());
        var new_var = cur + change;
        if (new_var<1) new_var = 1;
        if (new_var>100) new_var = 100;
        $('#travelform-travellerscount').val(new_var);
         $('body').trigger('change-filter');
      });

      function setSoultionParams(params) {
        params.forEach(function(param, i, arr) {
            switch(param.slug) {
                case 'cancel':
                    if (param.checked==1) {
                        $('input[name=param-'+param.id).prop('checked',true);
                        $('.cancel-progress.escape-travel').show();
                        $('.cancel-progress.escape-travel input.input_escape_sum').val(param.variant.amount);
                        $('.cancel-progress.escape-travel input.checkbox__input').prop('checked',((param.variant['sick-list']==1)?true:false));
                    } else {
                        $('input[name=param-'+param.id).prop('checked',false);
                        $('.cancel-progress.escape-travel').hide();
                        $('.cancel-progress.escape-travel input.input_escape_sum').val('');
                        $('.cancel-progress.escape-travel input.checkbox__input').prop('checked',false);
                    }
                    break;
                case 'sum':
                    variants =$('.additional-options__progress_sum .js-progress').data('variants');
                    variants = variants?variants.split(','):[];
                    var slider = $('.additional-options__progress_sum .js-progress').data('ionRangeSlider');
                    slider.update({
                        from:variants.indexOf(param.variant.name)
                    });
                    break;
                case 'pregnancy':
                    if (param.checked==1) {
                        $('input[name=param-'+param.id).prop('checked',true);
                        $('.pregnant-progress').show();
                        variants =$('.pregnant-progress .js-progress').data('variants');
                        variants = variants?variants.split(','):[];
                        var slider = $('.pregnant-progress .js-progress').data('ionRangeSlider');
                        slider.update({
                          from:variants.indexOf(param.variant)
                        });
                    } else {
                        $('input[name=param-'+param.id).prop('checked',false);
                        $('.pregnant-progress').hide();
                    }
                    break;
                default:
                    $('input[name=param-'+param.id).prop('checked',((param.checked==1)?true:false));
                break;
            }
        });
        $('body').trigger('change-filter');
      }
  });
");
