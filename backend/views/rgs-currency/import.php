<?php
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $updated integer */
/* @var $inserted integer */
/* @var $deleted integer */

$this->title = 'Валюты';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Импорт';
?>
<div class="currency-import">
    <div class="well">
        <p>Обновлено валют: <?= $updated ?></p>
        <p>Добавлено валют: <?= $inserted ?></p>
        <p>Удалено валют: <?= $deleted ?></p>
    </div>
</div>
