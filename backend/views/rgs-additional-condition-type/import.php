<?php
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $updated integer */
/* @var $inserted integer */
/* @var $deleted integer */

$this->title = 'Виды дополнительных условий';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Импорт';
?>
<div class="additional-condition-type-import">
    <div class="well">
        <p>Обновлено: <?= $updated ?></p>
        <p>Добавлено: <?= $inserted ?></p>
        <p>Удалено: <?= $deleted ?></p>
    </div>
</div>
