<?php
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $updated integer */
/* @var $inserted integer */
/* @var $deleted integer */
/* @var $skipped integer */
/* @var $skipped_arr array */

$this->title = 'Дополнительные условия';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Импорт';
?>
<div class="additional-condition-import">
    <div class="well">
        <p>Обновлено: <?= $updated ?></p>
        <p>Добавлено: <?= $inserted ?></p>
        <p>Удалено: <?= $deleted ?></p>
        <p>Пропущено: <?= $skipped ?></p>
        <pre><?= print_r($skipped_arr) ?></pre>
    </div>
</div>
