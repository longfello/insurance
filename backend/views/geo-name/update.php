<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\geo\models\GeoName */

$this->title = 'Редактирование информации о городе ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Города', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="geo-name-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
