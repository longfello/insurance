<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiAlphaStrah\models\Country */

$this->title = 'Редактировать страну: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Страны', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="program-update">

    <?= $this->render('_form', [
        'model' => $model,
        'id' => $id,
    ]) ?>

</div>
