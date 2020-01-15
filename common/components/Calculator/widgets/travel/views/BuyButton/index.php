<?php
  /**
   * @var $program \common\models\ProgramResult
   */
	$form_id = uniqid(sha1(json_encode($program)).'-');
?>
<form action="/api/<?= \common\components\Calculator\forms\prototype::SLUG_TRAVEL ?>/calc-choose-program.html" method="post" id="<?= $form_id ?>" class="ajax-reloader">
	<input type="hidden" name="program" value='<?= base64_encode(serialize($program)) ?>'>
	<input type="hidden" name="<?= Yii::$app->getRequest()->csrfParam ?>" value='<?= Yii::$app->getRequest()->getCsrfToken() ?>'>
	<button type='submit' class="button button_color_green button_size_m button_uppercase">Купить</button>
</form>
