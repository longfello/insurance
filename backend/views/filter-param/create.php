<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\components\Calculator\models\travel\FilterParam */

$this->title = 'Добавить параметр фильтра';
$this->params['breadcrumbs'][] = ['label' => 'Параметры фильтра', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="filter-param-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
