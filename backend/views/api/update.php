<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Api */

$this->title = 'Редактирование Api: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Api', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="api-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
