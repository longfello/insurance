<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CostInterval */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Интервал страховых сумм',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Интервалы страховых сумм'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="cost-interval-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
