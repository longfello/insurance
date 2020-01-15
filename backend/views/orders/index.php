<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-index">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'api_id' => [
              'attribute' => 'api_id',
              'value'     => function(\common\models\Orders $model){
                return $model->api->name;
              }
            ],
            'price' => [
              'attribute' => 'price',
              'value'     => function(\common\models\Orders $model){
	              return $model->currency->char_code.' '.$model->price;
              }
            ],
            'status' => [
              'attribute' => 'status',
              'value' => function(\common\models\Orders $model){
	              return $model->statusName;
              }
            ],
            // 'holder_id',
            // 'info:ntext',
            // 'calc_form:ntext',
            // 'program:ntext',
            'created_at',

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{view}'
            ],
        ],
    ]); ?>

</div>
