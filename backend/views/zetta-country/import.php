<?php
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Страны';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-import">
    <div class="well">
        <p>Обновлено стран: <?= $updated ?></p>
        <p>Добавлено стран: <?= $inserted ?></p>
        <p>Удалено стран: <?= $deleted ?></p>
    </div>
</div>
