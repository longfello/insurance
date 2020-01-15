<?php
/* @var $this yii\web\View */
/* @var $model \common\modules\ApiRgs\models\Sum */
/* @var $id integer */

$this->title = 'Редактировать сумму: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Суммы страхования', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
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
