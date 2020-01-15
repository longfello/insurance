<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\InsuranceType */

$this->title = 'Редактирование типа страхования: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Типы страхования', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="insurance-type-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
