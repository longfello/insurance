<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('backend', 'Дополнительные условия');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="additional-condition-index">
    <p>
        <?= Html::a(Yii::t('backend', 'Добавить'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'parent_id' => [
              'attribute' => 'parent_id',
              'value' => function($data){
                if ($data->parent){
	                return $data->parent_id.') '.$data->parent->name;
                } else {
	                return '-';
                }
              }
            ],
            'name',
            'description:ntext',
            'value',
            'type' => [
              'attribute' => 'type',
              'value' => function($data){
	              if ($data->type == 'koef') return 'Коэффициент';
	              if ($data->type == 'percent') return 'Процент от стоимости';
	              return '-';
              }
            ],
            'slug',
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
</div>
