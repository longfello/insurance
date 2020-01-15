<?php
/* @var $this yii\web\View */
/* @var $model \common\modules\ApiZetta\models\Risk */

$this->title = 'Редактировать риск: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Риски', 'url' => ['index']];
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
