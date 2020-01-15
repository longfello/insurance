<?php
/**
 * @title Калькулятор, результаты
 */
/* @var $this yii\web\View */
$this->title = Yii::$app->name;
xj\modernizr\ModernizrAsset::register($this);
?>
  <div class="calc page__inner">
    <div class="js-scroll-column page__left">
      <div class="company-list company-list_previews">
        <div class="company-list__item_wrapper"></div>
      </div>
    </div>
    <div data-scroll-speed="10" class="js-scroll-column page__right">
      <div class="page__right-inner">
        <div class="page__tabs tabs">
			    <?= \common\components\Calculator\Calculator::calcPageForm(); ?>
        </div>
        <div class="_visible helper"></div>
      </div>
    </div>
    <div class="fade-out">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 31.49 31.49" x="0px" y="0px" width="22px" height="22px">
        <path d="M21.205,5.007c-0.429-0.444-1.143-0.444-1.587,0c-0.429,0.429-0.429,1.143,0,1.571l8.047,8.047H1.111  C0.492,14.626,0,15.118,0,15.737c0,0.619,0.492,1.127,1.111,1.127h26.554l-8.047,8.032c-0.429,0.444-0.429,1.159,0,1.587  c0.444,0.444,1.159,0.444,1.587,0l9.952-9.952c0.444-0.429,0.444-1.143,0-1.571L21.205,5.007z" fill="#FFFFFF"></path>
      </svg>
    </div>
  </div>

<?php

$this->registerJs("$('form.insurance-find__body').submit();");
