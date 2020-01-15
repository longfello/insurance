<?php
/* @var $this yii\web\View */
/* @var $model \common\modules\ApiZetta\models\Product */
/* @var $id integer */

$this->title = 'Редактирование продукта: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Продукты', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<div class="product-update">

    <?=
    $this->render('_form', [
        'model' => $model,
        'id' => $id
    ]);
    ?>

</div>
