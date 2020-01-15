<?php
/* @var $this yii\web\View */
/* @var $model \common\modules\ApiRgs\models\Country */
/* @var $id integer */

$this->title = 'Редактировать страну: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Страны', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="country-update">

    <?=
    $this->render('_form', [
        'model' => $model,
        'id' => $id
    ]);
    ?>

</div>
