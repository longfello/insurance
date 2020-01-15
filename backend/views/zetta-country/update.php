<?php
/* @var $this yii\web\View */
/* @var $model \common\modules\ApiZetta\models\Country */

$this->title = 'Редактировать страну: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Страны', 'url' => ['index']];
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
