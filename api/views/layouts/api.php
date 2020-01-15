<?php
/**
 * Copyright (c) kvk-group 2018.
 */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

/* @var $this \yii\web\View */
/* @var $content string */

$this->beginContent('@api/views/layouts/_clear.php')
?>

<nav class="navbar navbar-inverse navbar-fixed-top">

  <div class="container">
    <div class="navbar-header">
      <a class="navbar-brand" href="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['site/index'], true) ?>">BulloSafe</a>
    </div>
  </div>
</nav>

<div class="container theme-showcase" role="main">
  <div class="row">
    <div class="col-xs-12">
      <?php echo $content ?>
    </div>
  </div>
</div><!-- /.container -->
<?php $this->endContent() ?>



