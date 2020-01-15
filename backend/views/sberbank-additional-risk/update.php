<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiSberbank\models\AdditionalRisk */

$this->title = 'Редактировать риск: '.$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Риски', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="risk-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
