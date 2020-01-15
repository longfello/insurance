<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Risk */

$this->title = 'Редактирование страхового риска: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Страховые риски', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="risk-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
