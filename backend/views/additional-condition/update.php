<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AdditionalCondition */

$this->title = Yii::t('backend', 'Редактировать {modelClass}: ', [
    'modelClass' => 'дополнительные условия',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Дополнительные условия'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Редактировать');
?>
<div class="additional-condition-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
