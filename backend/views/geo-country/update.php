<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GeoCountry */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Страны',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Страны'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Редактировать');
?>
<div class="geo-country-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
