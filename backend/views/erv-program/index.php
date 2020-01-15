<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Програмы страхования';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-index">

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'product_code',
            'tariff_code',
            'price',
            'summa',
            'region_id' =>[
	            'attribute' => 'region_id',
	            'value' => function($data){
		            if ($data->region_id){
			            return $data->region->name;
		            } else {
			            return '-';
		            }
	            }
            ],

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
</div>
