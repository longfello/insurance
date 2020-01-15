<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiErv\models\Risk */

$this->title = Yii::t('backend', 'Редактировать {modelClass}: ', [
    'modelClass' => 'риск',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Риски'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Редактировать');
?>
<div class="risk-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
