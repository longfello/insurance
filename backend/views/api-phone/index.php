<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $api_id integer */
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Телефоны страховой компании';
$this->params['breadcrumbs'][] = ['label' => 'Api', 'url' => ['/api/index']];;
$this->params['breadcrumbs'][] = ['label' => 'редактирование', 'url' => ['/api/update', 'id' => $api_id]];;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-phone-index">
    <p>
        <?= Html::a('Добавить телефон', ['create', 'api_id' => $api_id], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'phone',
            [
              'class'    => 'yii\grid\ActionColumn',
              'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
</div>
