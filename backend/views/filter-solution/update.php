<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\components\Calculator\models\travel\FilterSolution */

$this->title = 'Обновление решения: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Готовые решения', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="filter-solution-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'params' => $params
    ]) ?>

</div>
