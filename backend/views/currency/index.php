<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Валюты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-index">
    <p>
        <?= Html::a('Добавить валюту', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
  	        'char_code',
            'name',
            'value',
            'num_code',
            'nominal',
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{update}'
            ],
        ],
    ]); ?>
</div>
