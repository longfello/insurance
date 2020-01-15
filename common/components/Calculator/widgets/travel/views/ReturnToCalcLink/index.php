<?php
  /**
   * @var $program \common\models\ProgramResult
   */
  $type = \common\models\InsuranceType::findOne(['slug' => \common\components\Calculator\forms\TravelForm::SLUG_TRAVEL]);
?>
<form action="<?= \yii\helpers\Url::to(['page/view', 'slug' => $type->resultPage->slug]) ?>" method="post" class="ajax-reloader2">

	<?php
     /* echo $program->calc->dateFrom;
      echo $program->calc->dateTo;*/
  ?>

	<?php foreach ($program->calc->countries as $country){
		echo \yii\bootstrap\Html::activeHiddenInput($program->calc, 'countries', ['value' => $country, 'name' => 'TravelForm[countries][]']);
	} ?>
	<?= \yii\bootstrap\Html::activeHiddenInput($program, 'dateFrom', ['value' => $program->calc->dateFrom, 'name' => 'TravelForm[dateFrom]']); ?>
	<?= \yii\bootstrap\Html::activeHiddenInput($program, 'dateTo', ['value' => $program->calc->dateTo, 'name' => 'TravelForm[dateTo]']); ?>

	<?= \yii\bootstrap\Html::hiddenInput('form_scenario', 'home'); ?>
	<?= \yii\bootstrap\Html::hiddenInput('form_type', \common\components\Calculator\forms\prototype::SLUG_TRAVEL); ?>
	<?= \yii\bootstrap\Html::hiddenInput(Yii::$app->getRequest()->csrfParam, Yii::$app->getRequest()->getCsrfToken()); ?>

	<?php

	if ($program->calc->solution) {
		echo \yii\bootstrap\Html::hiddenInput('filter_solution', $program->calc->solution);
	}

	foreach($program->calc->params as $one){
	  /** @var $one \common\components\Calculator\models\travel\FilterParam */
	  if ($one->handler->checked){
		echo \yii\bootstrap\Html::hiddenInput('param-'.$one->id, 1);
	    if ($one->handler->variant) {
		    echo \yii\bootstrap\Html::hiddenInput('param-'.$one->id.'-variant', $one->handler->getVariantValue());
	    }
	  }
	}
	?>

	<button type='submit' class="button-as-plain-text company-chosen-list__back-to-top">Вернуться к выбору</button>
</form>
