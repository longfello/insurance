<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Amount */

$this->title = 'Редактирование страховой суммы: #' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Страховые суммы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="amount-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
