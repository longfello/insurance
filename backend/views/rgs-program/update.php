<?php
/* @var $this yii\web\View */
/* @var $model \common\modules\ApiRgs\models\Program */
/* @var $id integer */

$this->title = 'Редактировать программу: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Программы страхования', 'url' => ['index']];
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
