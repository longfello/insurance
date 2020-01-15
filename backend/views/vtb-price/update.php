<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Price */

$this->title = 'Редактирование страховой суммы #' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Программа', 'url' => ['/vtb-program/update', 'id' => $model->program_id]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="price-update">

    <?php echo $this->render('_form_update', [
        'model' => $model,
    ]) ?>

</div>
