<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 11.08.17
 * Time: 15:21
 *
 * @var $this \yii\web\View
 * @var $page \common\models\Page
 * @var $type \common\models\InsuranceType
 * @var $widget string
 * @var $availableTypes \common\models\InsuranceType[]
 *
 */

?>

<?php if ($widget) { ?>
  <div class="insurance-find__type insurance-find__type_soon active" id="calc-<?= $type->slug ?>">
    <div class="insurance-find-form insurance-find-form_<?= $type->slug ?>">
	    <?= $widget ?>
    </div>
  </div>
<?php } ?>


<?php foreach($availableTypes as $one) { ?>
  <?php if (!$widget || ($one->slug != $type->slug)){ ?>
    <?php $url = $one->calcPage?$one->calcPage->createUrl():false; ?>
    <?php if ($url){ ?>
      <div class="insurance-find__type insurance-find__type_<?= $one->slug ?> insurance-find__type_soon" id="calc-<?= $one->slug ?>">
        <div class="insurance-find__header">
          <a href="<?= $url?$url:'#' ?>" class="insurance-find__title insurance-find__title_white title title_size_l title_uppercase"><?= $one->name ?></a>
          <div class="filter__desc"><?= $one->description ?></div>
          <div class="insurance-preview-image insurance-preview-image_<?= $one->slug ?>"></div>
        </div>
        <div class="insurance-image insurance-image_<?= $one->slug ?>"></div>
      </div>
    <?php } ?>
  <?php } ?>
<?php } ?>
