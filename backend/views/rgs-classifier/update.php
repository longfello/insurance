<?php
/* @var $this yii\web\View */
/* @var $model \common\modules\ApiRgs\models\Classifier */
/* @var $id integer */

$this->title = 'Редактирование справочника: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<div class="classifier-update">

    <?=
    $this->render('_form', [
        'model' => $model,
        'id' => $id
    ]);
    ?>

</div>
