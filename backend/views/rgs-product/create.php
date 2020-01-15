<?php
/* @var $this yii\web\View */
/* @var $model common\modules\ApiRgs\models\Product */

$this->title = 'Добавление продукта';
$this->params['breadcrumbs'][] = ['label' => 'Продукты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-create">

    <?php
    echo $this->render('_form', [
        'model' => $model,
    ]);
    ?>

</div>
