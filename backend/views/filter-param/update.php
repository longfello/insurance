<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\components\Calculator\models\travel\FilterParam */

$this->title = 'Редактирование параметр фильтра: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Параметры фильтра', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="filter-param-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
