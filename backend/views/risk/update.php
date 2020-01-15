<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Risk */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Риск',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Риски'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="risk-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
