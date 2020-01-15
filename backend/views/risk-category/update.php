<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RiskCategory */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Категорию рисков',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Категории рисков'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="risk-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
