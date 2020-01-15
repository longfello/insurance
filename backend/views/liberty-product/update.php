<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiLIberty\models\Product */

$this->title                   = 'Редактирование продукта: ' . ' ' . $model->productName;
$this->params['breadcrumbs'][] = [ 'label' => 'Продукты', 'url' => [ 'index' ] ];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
    <div class="product-update">

    <?php echo $this->render( '_form', [
        'model' => $model
    ] ) ?>

    </div>
