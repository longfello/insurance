<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Параметры фильтра';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="filter-param-index">

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'type',
            'risk_id' => [
              'format' => 'raw',
              'label'  => 'Соответствует риску из внутреннего справочника',
              'value'  => function($model){
                  /** @var $model \common\components\Calculator\models\travel\FilterParam */
                  if ($model->risk) {
                    return $model->risk->name;
                  }
                  return '-';
              }
            ],
            'variants',
            'change_desc' => [
                'attribute' => 'change_desc',
                'value'      => function ( $model ) {
                    return ($model->change_desc==1)?"Да":"Нет";
                }
            ],
            'sort_order',

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
</div>
