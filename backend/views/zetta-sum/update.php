<?php
/* @var $this yii\web\View */
/* @var $model \common\modules\ApiZetta\models\Sum */

$this->title = 'Редактировать сумму страхования: ' . $model->sum;
$this->params['breadcrumbs'][] = ['label' => 'Суммы страхования', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->sum;
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="sum-update">

    <?=
    $this->render('_form', [
        'model' => $model,
        'id' => $id
    ]);
    ?>

</div>
