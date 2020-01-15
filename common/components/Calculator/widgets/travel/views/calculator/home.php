<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\components\Calculator\forms\TravelForm
 *
 */

?>

<?php
  $type = \common\models\InsuranceType::findOne(['slug' => \common\components\Calculator\forms\TravelForm::SLUG_TRAVEL]);

  $form = \frontend\components\ActiveForm::begin([
	'action' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['page/view', 'slug' => $type->resultPage->slug], true),
//	'action' => '/calc.html',
	'method' => 'post',
  'form_type' => \common\components\Calculator\forms\prototype::SLUG_TRAVEL,
	  'enableClientValidation' => true,
  'scenario' => 'home',
	'options' => [
		'class'  => 'insurance-find__body',
	]
  ]);
?>

	<div class="filter insurance-find__filter">
		<div href="#" class="insurance-find__title insurance-find__title_grey title title_size_l">Страхование путешественников</div>
		<div class="filter__desc">
				Туристическая страховка - это гарантия покрытия медицинских и медико-транспортных расходов во время путешествий и организация экстренной медицинской помощи. Во многих странах наличие такого полиса - обязательное условие при выдаче визы.
		</div>
	  <?= \yii\bootstrap\Html::errorSummary($model); ?>
		<div class="filter__country">
			<div class="filter__label filter__label_country title title_size_s">Страна
			</div>

			<div class="filter__select-help">
        <?=$form->field($model, 'countries')->dropDownList( \yii\helpers\ArrayHelper::map(
	        \common\models\GeoCountry::find()->orderBy(['type' => SORT_DESC, 'name' => SORT_ASC])->all(),
	        'id',
	        'name'
        ), [
	        'class' => "js-select select",
	        'style' => "width:100%",
					'multiple' => "multiple"
        ]);?>
			</div>
		</div>
		<div class="filter__dates">
			<div class="group-input">
				<div class="filter__label title title_size_s">Туда</div>
				<div class="date-field filter__input">
          <?= $form->field($model, 'dateFrom')->textInput([
	          'class' => 'input input_color_black input_size_m js-datepicker',
	          'autocomplete' => 'off',
						'readonly'     => 'readonly'
          ]); ?>
					<div class="date-field__icon">
						<svg class="icon icon_calendar ">
							<use xlink:href="#icon-calendar"></use>
						</svg>
					</div>
			<?= \yii\bootstrap\Html::error($model, 'dateFrom'); ?>
				</div>
			</div>
			<div class="group-input">
				<div class="filter__label title title_size_s">Обратно</div>
				<div class="date-field filter__input">
          <?= $form->field($model, 'dateTo')->textInput([
            'class' => 'input input_color_black input_size_m js-datepicker',
            'autocomplete' => 'off',
						'readonly'     => 'readonly'
          ]); ?>
					<div class="date-field__icon">
						<svg class="icon icon_calendar ">
							<use xlink:href="#icon-calendar"></use>
						</svg>
					</div>
				</div>
			</div>
		</div>
		<div class="filter__submit">
			<button type="submit" class="button button_color_green button_size_l button_uppercase">Найти страховку</button>
		</div>
	</div>
  <div class="insurance-image insurance-image_travel insurance-image_travel_calc">
      <img class="insurance-image" src="/img/palm.png">
  </div>
<?php \frontend\components\ActiveForm::end() ?>
