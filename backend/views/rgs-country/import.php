<?php
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $updated integer */
/* @var $inserted integer */
/* @var $deleted integer */

$this->title = 'Страны';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Импорт';
?>
<div class="country-import">
    <div class="well">
        <p>Обновлено стран: <?= $updated ?></p>
        <p>Добавлено стран: <?= $inserted ?></p>
        <p>Удалено стран: <?= $deleted ?></p>
    </div>
</div>
