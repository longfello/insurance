<?php
  /**
   * @var $model \common\models\ProgramResult
   */
?>
<div class="calc page__inner page__inner_detail">
	<div class="js-scroll-column page__left">
		<div class="company-list company-detail">
			<div class="company-list__title title title_size_xl">Страхование путешественников</div>
			<div class="company-list__item_wrapper">
				<div class="company-chosen-list">
					<div class="company-chosen-list__item">
						<div class="company-chosen-list__item-header">
							<div class="company-chosen-list__title title title_size_l">Вы выбрали:
							</div>
							<?= \common\components\Calculator\widgets\travel\ReturnToCalcLinkWidget::widget(['program' => $model]) ?>
						</div>
						<div class="company company_chosen js-company _open">
							<div class="company__header">
								<div class="company__logo"><img src="<?= $model->thumbnail_url ?>">
								</div>
								<div class="company__price">
										<span class="price-value"><?= $model->cost ?></span>
									<span class="ruble-icon"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="21" x="0px" y="0px" viewBox="0 0 330 330">
										<path id="XMLID_449_" d="M180,170c46.869,0,85-38.131,85-85S226.869,0,180,0c-0.183,0-0.365,0.003-0.546,0.01h-69.434
	c-0.007,0-0.013-0.001-0.019-0.001c-8.284,0-15,6.716-15,15v0.001V155v45H80c-8.284,0-15,6.716-15,15s6.716,15,15,15h15v85
	c0,8.284,6.716,15,15,15s15-6.716,15-15v-85h55c8.284,0,15-6.716,15-15s-6.716-15-15-15h-55v-30H180z M180,30.01
	c0.162,0,0.324-0.003,0.484-0.008C210.59,30.262,235,54.834,235,85c0,30.327-24.673,55-55,55h-55V30.01H180z"/>
									</svg></span>
								</div>
								<div class="company__chosen">
									<div class="company__chosen-icon">
										<svg class="icon icon_check ">
											<use xlink:href="#icon-check"></use>
										</svg>
									</div>
								</div>
							</div>
							<div class="company__info">
								<div class="company__info-more js-company-more-btn">
									<div class="company__info-more-link link link_color_green">Подробнее<i class="icon icon_pointer-down-green"></i>
									</div>
								</div>
								<div class="company__info-expert">
									<div class="company__info-title">Рейтинг "Эксперт РА"
									</div>
									<div class="company__info-rating"><?= $model->rate_expert ?></div>
								</div>
								<div class="company__info-asn">
									<div class="company__info-title">Рейтинг АСН
									</div>
									<div class="company__info-rating"><?= $model->rate_asn ?></div>
								</div>
							</div>
							<div class="company__content js-company-content">
								<div class="company__pdf-list">
									<div class="company__pdf-item">
										<div class="pdf">
											<div class="pdf__icon">PDF
											</div><a href="<?= $model->rule_url ?>" target="_blank" class="link link_color_green pdf__link">Правила страхования</a>
										</div>
									</div>
									<div class="company__pdf-item company__pdf-item_sep">
										<div class="pdf">
											<div class="pdf__icon">PDF
											</div><a href="<?= $model->police_url ?>" target="_blank" class="link link_color_green pdf__link">Образец полиса</a>
										</div>
									</div>
								</div>
								<div class="tabs tabs_company">
									<div class="tabs__header">
										<div data-tab="included" class="js-tabs-item tabs__item tabs__item_included">
											<div class="title title_size_m">Что входит</div>
										</div>
										<div data-tab="what-to-do" class="_active js-tabs-item tabs__item tabs__item_what-to-do">
											<div class="title title_size_m">Действия при страховом случае
											</div>
										</div>
									</div>
									<div class="tabs__content">
										<div data-content="included" class="js-content-item tabs__content-item">
											<div class="company__included">
												<div class="company__included-title title title_size_m">Что входит в покрытие</div>
												<table class="company__included-list">
                                                    <?php foreach($model->risks as $name => $amount) { ?>
                                                        <?php if (!empty($name)) {?>
                                                            <tr class="company__included-row">
                                                              <td class="company__included-name"><?= $name ?></td>
                                                              <td class="company__included-price"><?= ($amount>0)?$amount."  &euro;":""; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } ?>
												</table>
											</div>
										</div>
										<div data-content="what-to-do" class="_active js-content-item tabs__content-item">
											<div class="what-to-do"><?= $model->actions ?></div>
										</div>
									</div>
								</div>
								<div class="company__footer">
									<div class="company__phones">
				        	  <?php foreach($model->phones as $name => $phone) { ?>
                      <div class="company__phones-item">
                        <div class="company__phones-title"><?= $name ?></div>
                        <div class="company__phones-number"><?= $phone ?></div>
                      </div>
        					  <?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div data-scroll-speed="10" class="js-scroll-column page__right">
		<div class="page__right-inner  page__right-inner_choosen">
			<div class="page__tabs tabs">

        <?php
        $form = \frontend\components\ActiveForm::begin([
	        'id' => 'calc-prepay-form',
//	        'action' => '/calc-enter-payer.html',
	        'action' => '/api/'.\common\components\Calculator\forms\prototype::SLUG_TRAVEL.'/calc-pay.html',
          'form_type' => \common\components\Calculator\forms\prototype::SLUG_TRAVEL,
	        'scenario' => \common\components\Calculator\forms\prototype::SCENARIO_PREPAY,
	        'method' => 'post',
	        'options' => [
		        'class'  => 'insurance-find__body ajax-reloader',
		        'data-target' => '.page__inner',
	        ]
        ]);
        ?>
          <input type="hidden" name="program" value='<?= base64_encode(serialize($model)) ?>'>

          <div class="_open dropdown form-step form-step-1">
            <div class="dropdown__content">
              <div class="dropdown__title title title_size_l">Данные о путешественниках</div>
              <div class="travelers-data">
                <div class="js-travelers-list travelers-data__add-card-list"></div>
                <div class="travelers-data__add-new">
                  <div class="button button_color_gray js-add-new-traveler" data-max="<?= $model->getApi()->getModule()->maxTravellersCount; ?>">
                    <svg class="icon icon_man "><use xlink:href="#icon-man"></use></svg>Добавить еще путешественника
                  </div>
                </div>
                <div class="travelers-data__pay">
                  <div class="travelers-data__pay-price">
                    <div class="travelers-data__pay-price-title">Стоимость страховки</div>
                    <div class="travelers-data__pay-price-number">
                        <div><?= $model->cost ?></div>
                      <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="21" x="0px" y="0px" viewBox="0 0 330 330">
                        <path id="XMLID_449_" d="M180,170c46.869,0,85-38.131,85-85S226.869,0,180,0c-0.183,0-0.365,0.003-0.546,0.01h-69.434
              c-0.007,0-0.013-0.001-0.019-0.001c-8.284,0-15,6.716-15,15v0.001V155v45H80c-8.284,0-15,6.716-15,15s6.716,15,15,15h15v85
              c0,8.284,6.716,15,15,15s15-6.716,15-15v-85h55c8.284,0,15-6.716,15-15s-6.716-15-15-15h-55v-30H180z M180,30.01
              c0.162,0,0.324-0.003,0.484-0.008C210.59,30.262,235,54.834,235,85c0,30.327-24.673,55-55,55h-55V30.01H180z"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="travelers-data__pay-btn">
                    <button type="button" class="button button_color_green button_size_l button_uppercase js-accordion" data-target=".form-step-2">Оплатить страховку</button>
                    <div class="travelers-data__submit-icon">
                      <svg class="icon icon_triangle-pointer ">
                        <use xlink:href="#icon-triangle-pointer"></use>
                      </svg>
                    </div>
                  </div>
                </div>
                <div class="travelers-data__payment-logos">
                    <img src="/img/payment-systems.png" class="travelers-data__logos-banner">
                </div>
              </div>
            </div>
          </div>
          <div class="_open dropdown form-step form-step-2 hidden">
            <div class="dropdown__content">
              <div class="dropdown__title title title_size_l">Покупатель</div>
              <div class="traveler-buyer">
                <div class="traveler-buyer__row">
                  <div class="traveler-buyer__item traveler-buyer__item_phone">
                    <div class="traveler-buyer__label">Телефон</div>
                    <div class="traveler-buyer__input">
                        <select id="country-code" class="js-select-country form-control traveler-buyer__country-code" name="countryCode">
                            <option value="rus" data-code="+7">Россия</option>
                            <option value="ukr" data-code="+380">Украина</option>
                            <option value="kz" data-code="+7">Казахстан</option>
                        </select>
                  <?= \yii\bootstrap\Html::activeTextInput($model->calc->payer, 'phone', [
                    'class'       => "input input_color_gray input_size_m",
                    'name'        => 'payer[phone]',
                    'data-inputmask' => '"alias": "phone"'
                  ]) ?>
                    </div>
                    <div class="error-summary form-group">
                      <div id='travelform-phone'></div>
                      <div class='help-block'></div>
                    </div>
                  </div>
                  <div class="traveler-buyer__item traveler-buyer__item_mail">
                    <div class="traveler-buyer__label">E-mail</div>
                    <div class="traveler-buyer__input">
              <?= \yii\bootstrap\Html::activeTextInput($model->calc->payer, 'email', [
                'placeholder' => 'example@yandex.ru',
                'class'       => "input input_color_gray input_size_m",
                'name'        => 'payer[email]'
              ]) ?>
                    </div>
                    <div class="error-summary form-group">
                      <div id='travelform-email'></div>
                      <div class='help-block'></div>
                    </div>
                  </div>
                </div>
                <div class="traveler-buyer__row">
                  <div class="traveler-buyer__item traveler-buyer__item_lname">
                    <div class="traveler-buyer__label">Фамилия</div>
                    <div class="traveler-buyer__input">
              <?= \yii\bootstrap\Html::activeTextInput($model->calc->payer, 'last_name', [
                'placeholder' => 'Ivanov',
                'class'       => "input input_color_gray input_size_m ".(($model->api_id!=3 && $model->api_id!=6)?"latin-only":""),
                'name'        => 'payer[last_name]'
              ]) ?>
                    </div>
                    <div class="error-summary form-group">
                      <div id='travelform-last_name'></div>
                      <div class='help-block'></div>
                    </div>
                  </div>
                  <div class="traveler-buyer__item traveler-buyer__item_fname">
                    <div class="traveler-buyer__label">Имя</div>
                    <div class="traveler-buyer__input">
              <?= \yii\bootstrap\Html::activeTextInput($model->calc->payer, 'first_name', [
                'placeholder' => 'Ivan',
                'class'       => "input input_color_gray input_size_m ".(($model->api_id!=3 && $model->api_id!=6)?"latin-only":""),
                'name'        => 'payer[first_name]'
              ]) ?>
                    </div>
                    <div class="error-summary form-group">
                      <div id='travelform-first_name'></div>
                      <div class='help-block'></div>
                    </div>

                  </div>
                </div>
                <div class="traveler-buyer__row">
                  <div class="traveler-buyer__item traveler-buyer__item_birth">
                    <div class="traveler-buyer__label">Дата рождения</div>
                    <div class="date-field filter__input">
              <?= \yii\bootstrap\Html::activeTextInput($model->calc->payer, 'birthday', [
                'data-view'   => "months",
                'class'       => "date-field__input input input_size_m js-datepicker",
                'name'        => 'payer[birthday]',
                'readonly'    => 'readonly'
              ]) ?>
                      <div class="date-field__icon">
                        <svg class="icon icon_calendar ">
                          <use xlink:href="#icon-calendar"></use>
                        </svg>
                      </div>
                      <div class="error-summary form-group">
                        <div id='travelform-birthday'></div>
                        <div class='help-block'></div>
                      </div>

                    </div>
                  </div>
                    <div class="traveler-buyer__item traveler-buyer__item_gender">
                        <div class="traveler-buyer__label">Пол</div>
                        <div class="date-field filter__input">
                            <?= \yii\bootstrap\Html::activeDropDownList($model->calc->payer, 'gender', [
                                'male'=>'Мужской',
                                'female'=>'Женский',
                            ],[
                                'prompt'      => 'Выберите пол',
                                'class'       => "input input_color_gray input_size_m",
                                'name'        => 'payer[gender]'
                            ]) ?>
                            <div class="error-summary form-group">
                                <div id='travelform-gender'></div>
                                <div class='help-block'></div>
                            </div>
                        </div>
                    </div>
                  <div class="traveler-buyer__item traveler-buyer__item_pasport">
                    <div class="traveler-buyer__label">Серия и номер (загранпаспорт)
                    </div>
                    <!--<div class="traveler-buyer__input traveler-buyer__input_serial">
              <?= \yii\bootstrap\Html::activeTextInput($model->calc->payer, 'passport_seria', [
                'placeholder' => '0000',
                'class'       => "input input_color_gray input_size_m",
                'name'        => 'payer[passport_seria]'
              ]) ?>
                    </div>-->
                    <div class="traveler-buyer__input traveler-buyer__input_number">
              <?= \yii\bootstrap\Html::activeTextInput($model->calc->payer, 'passport_no', [
                'placeholder' => 'Серия и номер пасспорта',
                'class'       => "input input_color_gray input_size_m",
                'name'        => 'payer[passport_no]'
              ]) ?>
                    </div>
                    <div class="error-summary form-group">
                      <div id='travelform-passport_no'></div>
                      <div class='help-block'></div>
                    </div>
                    <div class="error-summary form-group">
                      <div id='travelform-passport_seria'></div>
                      <div class='help-block'></div>
                    </div>
                  </div>
                </div>
                <div class="traveler-buyer__row">
                  <label class="checkbox">
                    <input type="checkbox" name='TravelForm[agree]' class="checkbox__input"/>
                    <span class="checkbox__icon"></span><span class="checkbox__label">Я согласен с <a class="link link_color_green" href="/page/rules.html" target="_blank">условиями передачи информации</a></span>
                  </label>
                  <div class="error-summary form-group">
                    <div id='travelform-agree'></div>
                    <div class='help-block'></div>
                  </div>
                </div>
                <div class="traveler-buyer__pay">
                  <div class="traveler-buyer__pay-price">
                    <div class="traveler-buyer__pay-price-title">Стоимость страховки</div>
                    <div class="traveler-buyer__pay-price-number">
                        <div><?= $model->cost ?></div>
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="21" x="0px" y="0px" viewBox="0 0 330 330">
                          <path id="XMLID_449_" d="M180,170c46.869,0,85-38.131,85-85S226.869,0,180,0c-0.183,0-0.365,0.003-0.546,0.01h-69.434
                c-0.007,0-0.013-0.001-0.019-0.001c-8.284,0-15,6.716-15,15v0.001V155v45H80c-8.284,0-15,6.716-15,15s6.716,15,15,15h15v85
                c0,8.284,6.716,15,15,15s15-6.716,15-15v-85h55c8.284,0,15-6.716,15-15s-6.716-15-15-15h-55v-30H180z M180,30.01
                c0.162,0,0.324-0.003,0.484-0.008C210.59,30.262,235,54.834,235,85c0,30.327-24.673,55-55,55h-55V30.01H180z"></path>
                        </svg>
                    </div>
                  </div>
                  <div class="traveler-buyer__pay-btn">
                    <button type="submit" class="button button_color_green button_size_l button_uppercase">Оплатить</button>
                    <div class="traveler-buyer__submit-icon">
                      <svg class="icon icon_triangle-pointer ">
                        <use xlink:href="#icon-triangle-pointer"></use>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        <?php \frontend\components\ActiveForm::end(); ?>
			</div>
			<div class="_visible helper">
				<!--<div class="helper__item">
					<div class="helper__time">4 мин назад</div>
				</div>-->
			</div>
		</div>
	</div>
</div>
<div class="filters-button-m">
    <svg class="icon icon_man ">
        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-man"></use>
    </svg>
</div>
<template id="addCard">
  <div class="add-card page__add-card">
    <div class="add-card__counter">{{counter}}</div>
    <div class="add-card__close js-add-card-close">
      <svg class="icon icon_close ">
        <use xlink:href="#icon-close"></use>
      </svg>
    </div>
    <div class="add-card__last-name">
      <div class="add-card__label">Фамилия латиницей</div>
      <div class="add-card__input">
	      <?= \yii\bootstrap\Html::activeTextInput($model->calc->payer, 'last_name', [
		      'class'       => "input input_color_gray input_size_m  latin-only",
		      'name'        => 'traveller[last_name][]'
	      ]) ?>
        <div class="error-summary form-group">
          <div id='travelform-last_name-{{counter}}'></div>
          <div class='help-block'></div>
        </div>
      </div>
    </div>
    <div class="add-card__first-name">
      <div class="add-card__label">Имя латиницей</div>
      <div class="add-card__input">
	      <?= \yii\bootstrap\Html::activeTextInput($model->calc->payer, 'first_name', [
		      'class'       => "input input_color_gray input_size_m  latin-only",
		      'name'        => 'traveller[first_name][]'
	      ]) ?>
        <div class="error-summary form-group">
          <div id='travelform-first_name-{{counter}}'></div>
          <div class='help-block'></div>
        </div>
      </div>
    </div>
    <div class="add-card__birthday">
      <div class="add-card__label">Дата рождения</div>
      <div class="date-field">
	      <?= \yii\bootstrap\Html::activeTextInput($model->calc->payer, 'birthday', [
					'data-view'   => "months",
		      'class'       => "date-field__input input input_size_m js-datepicker",
		      'name'        => 'traveller[birthday][]',
          'readonly'    => 'readonly'
	      ]) ?>
        <div class="date-field__icon">
          <svg class="icon icon_calendar ">
            <use xlink:href="#icon-calendar"></use>
          </svg>
        </div>

        <div class="error-summary form-group">
          <div id='travelform-birthday-{{counter}}'></div>
          <div class='help-block'></div>
        </div>

      </div>
    </div>
    <div class="add-card__gender">
        <div class="add-card__label">Пол</div>
        <div class="add-card__input">
            <?= \yii\bootstrap\Html::activeDropDownList($model->calc->payer, 'gender', [
                'male'=>'Мужской',
                'female'=>'Женский',
            ],[
                  'prompt'      => 'Выберите пол',
                  'class'       => "input input_color_gray input_size_m",
                  'name'        => 'traveller[gender][]'
            ]) ?>
            <div class="error-summary form-group">
                <div id='travelform-gender-{{counter}}'></div>
                <div class='help-block'></div>
            </div>
        </div>
    </div>
  </div>
</template>


<script type="text/javascript">
  var last_query = false;

  $(document).ready(function(){
      var max_travellers = <?= $model->getApi()->getModule()->maxTravellersCount; ?>;
      var col_travellers = <?=  $model->calc->travellersCount; ?>;

      col_travellers = (col_travellers>max_travellers)?max_travellers:col_travellers;

      if (col_travellers==max_travellers) {
          $('.js-add-new-traveler').hide();
      }

      for(var i=1; i<=col_travellers;i++){
        addTraveller();
      }

      <?php if ($model->api->name == 'Zetta') { ?>
          $(document).on('change', '[name="traveller[gender][]"]', function () {
              reloadPrice();
          });
      <?php } ?>

      if($('.js-datepicker').length) {
          var cur_date = new Date();
          cur_date.setFullYear( cur_date.getFullYear() - 90 ),
          $(".js-datepicker").datepicker({
              startDate: new Date('1970'),
              autoClose: true,
              minDate: cur_date,
              onSelect: function(formattedDate, date, inst) {
                  reloadPrice();
              }
          }).on('change', function(){
              var data = $(this).val();
              var parts = data.split('.');

              if(parts.length==3){
                  $(this).datepicker().data('datepicker').selectDate(new Date(parts[2],parts[1]-1,parts[0]));
              }

              reloadPrice();
          });
      }

      $('.js-accordion').on('click', function(e){
          $(this).parents('.travelers-data__pay').addClass('hidden');
          $($(this).data('target')).removeClass('hidden');
          var paym = $(".travelers-data__payment-logos").clone();
          $(".travelers-data__payment-logos").remove();
          $(".traveler-buyer").append(paym);

          $(".traveler-buyer__item_birth").find(".js-datepicker").datepicker({
              startDate: new Date('1970'),
              autoClose: true
          });

          $("#personinfo-phone").inputmask({
              clearMaskOnLostFocus: false
          });
          $("#personinfo-phone").val($('#country-code').find('option').first().data('code'));


      });

      $('.filters-button-m').on("click", function () {
          $('.page__right').addClass('page__right_roll');
          $('.page__inner_detail').css({'overflow': 'hidden'});
      });

      function formatState (state) {
          var code = $("option[value='"+ state.id +"']").data("code");
          if (!state.id) {
              return state.text;
          }
          var baseUrl = "/img/flags";
          var $state = $(
              '<span data-code="' + code + '"><img src="' + baseUrl + '/' + state.element.value.toLowerCase() + '.png" class="img-flag" /><span>' + code + '</span></span>'
          );
          $('#personinfo-phone').val(code);

          return $state;
      };

      /*function formatResult (state) {
          var code = $("option[value='"+ state.id +"']").data("code");
          if (!state.id) {
            return state.text;
          }
          var baseUrl = "/img/flags";
          var $state = $(
            '<span data-code="' + code + '"><img src="' + baseUrl + '/' + state.element.value.toLowerCase() + '.png" class="img-flag" /><span>' + code + '</span></span>'
          );

          return $state;
      };*/

      $('.js-select-country').select2({
          minimumResultsForSearch: -1,
          templateSelection: formatState/*,
          templateResult: formatResult*/
      });

  });
</script>

<script>
  //latin-only validation on calc-enter-payer.php
  $(document).ready(function(){
    if($(".latin-only").length > 0)
    {

        $(".latin-only").keydown(function() {
          var text = $(this).val();
          var pattern = /[а-яА-ЯїЇєЄіІёЁ]/g;
          var result = pattern.test(text);
          if(result) {
              $(this).next('.error-summary').addClass('has-error').end().closest('.add-card__input').find('.help-block').html('Допустимы только латинские символы');
          }
          else {
              $(this).next('.error-summary').removeClass('has-error').end().closest('.add-card__input').find('.help-block').html('');
          }
          text = text.replace(pattern, "");
          $(this).val(text);

        });
    }
  });
</script>
