<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * @title Обычная статическая страница
 * @var $this \yii\web\View
 * @var $exception \Exception
 */
$this->title = 'Error occurred';
?>
<div class="content">
  <h2>Error occured</h2>
  <p><?= $exception->getMessage() ?></p>
  <p>Http Code: <?= Yii::$app->response->statusCode ?></p>
  <p>Error Code: <?= Yii::$app->response->errorCode ?></p>

  <?php

    var_dump($exception->getTraceAsString());

  ?>
</div>