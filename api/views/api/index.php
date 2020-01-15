<?php
/**
 * @var $this \yii\web\View
 */
$this->title = 'Документация API страхования';
?>

<style>
  .table thead {
    background-color: #efefef;

  }
</style>

<div class="content">
    <?php Yii::$app->rest->compileDocumentation(); ?>
</div>

<?php

  $this->registerJs("$('table').addClass('table table-hover table-bordered');");