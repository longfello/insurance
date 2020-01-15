<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Валюты');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'currencyId',
            'currency',
            'currencyName',

	        ['class' => 'yii\grid\ActionColumn', 'template' => ''],
        ],
    ]); ?>
</div>
