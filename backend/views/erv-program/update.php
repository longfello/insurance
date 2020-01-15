<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiErv\models\Program */

$this->title = 'Редактировать программу: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="program-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
