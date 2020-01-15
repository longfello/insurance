<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Price */

$this->title = 'Добавление страховой суммы в программу страхования';
$this->params['breadcrumbs'][] = ['label' => 'Программа', 'url' => ['/vtb-program/update', 'id' => $model->program_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="price-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
