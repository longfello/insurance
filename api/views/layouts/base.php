<?php
/**
 * Copyright (c) kvk-group 2017.
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
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/">BulloSafe API</a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
        <li class="active"><a href="/">Документация</a></li>
        <li><a href="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['site/index'], true) ?>">Сайт</a></li>
      </ul>
    </div><!--/.nav-collapse -->
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



