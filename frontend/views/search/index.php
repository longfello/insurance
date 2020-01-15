<?php
/* @var $this yii\web\View */
$this->title = Yii::$app->name;
?>
<div class="page__inner page__inner_index">
  <div data-scroll-speed="10" class="js-scroll-column page__left">
    <?= $this->renderFile('@frontend/views/layouts/default-left.php') ?>
  </div>
  <div class="js-scroll-column page__right">
    <div class="insurance-find">
      <div class="insurance-find__type">
        <div class="insurance-find__header js-local-hrefs">
          <div class="insurance-find__title title title_size_l title_uppercase">Результаты поиска</div>
        </div>
        <div class="content title_size_m">
          <div class="serch_results">
            <?php foreach ($models as $model){ ?>
              <div class="result-item">
                <a href="/page/<?=$model->slug?>.html">
                  <h2><?= $model->title ?></h2>
                  <p><?= \frontend\components\SearchHighlighter::getFragment(strip_tags($model->body), $q) ?></p>
                </a>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
