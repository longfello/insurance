<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\components\Calculator\models\travel\FilterSolution */

$this->title = 'Создать готовое решение';
$this->params['breadcrumbs'][] = ['label' => 'Готовые решения', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="filter-solution-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'params' => []
    ]) ?>

</div>
