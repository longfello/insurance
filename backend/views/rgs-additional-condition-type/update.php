<?php
/* @var $this yii\web\View */
/* @var $model \common\modules\ApiRgs\models\AdditionalConditionType */
/* @var $id integer */

$this->title = 'Редактировать вид доп. условия: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Виды дополнительных условий', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="additional-condition-type-update">

    <?=
    $this->render('_form', [
        'model' => $model,
        'id' => $id
    ]);
    ?>

</div>
