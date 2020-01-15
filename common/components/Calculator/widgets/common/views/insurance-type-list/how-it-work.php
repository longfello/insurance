<?php
  /**
   * @var $models \common\models\InsuranceType[]
   * @var $this \yii\web\View
   */
$slug = str_replace(['page/', '.html'], '', Yii::$app->request->pathInfo);
$pageFounded = false;
?>
<ul class="menu-how-work__item">
	<?php $active = ($slug == 'insurance-policy'); ?>
	<?php $pageFounded = $pageFounded || $active; ?>
  <li class="menu-how-work__link menu-how-work__sublink"><a class="link <?= $active?"active":"" ?>"  href="/page/insurance-policy.html">Как выбрать страховой полис?</a></li>
	<?php foreach ($models as $model) { ?>
    <?php if ($model->aboutPage){ ?>
      <?php
		    $active = false;
        if ($model->aboutPage->slug == $slug) {
	        $active = true;
          $pageFounded = true;
        }
      ?>
  		<li class="menu-how-work__link menu-how-work__sublink"><a class="link <?= $active?"active":"" ?>" href="<?= $model->aboutPage->createUrl() ?>"><?= $model->aboutPage->title ?></a></li>
    <?php } ?>
	<?php } ?>
  <?php $active = !$pageFounded; ?>
  <li class="menu-how-work__link menu-how-work__sublink"><a class="link <?= $active?"active":"" ?>"  href="/page/work-service.html">Как работает сервис BulloSafe</a></li>
</ul>
