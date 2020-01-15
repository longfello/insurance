<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Period */

$this->title = 'Редактирование периода: ' . ' ' . $model->from.'-'.$model->to;
$this->params['breadcrumbs'][] = ['label' => 'Периоды', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->from.'-'.$model->to, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="period-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
