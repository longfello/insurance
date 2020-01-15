<?php
/* @var $this yii\web\View */
/* @var $model \common\modules\ApiZetta\models\Program */

$this->title = 'Редактировать программу страхования: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Програмы страхования', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="program-update">

    <?=
    $this->render('_form', [
        'model' => $model,
        'id' => $id
    ]);
    ?>

</div>
