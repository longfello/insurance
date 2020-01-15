<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Риски');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="risk-index">

    <p>
        <?= Html::a(Yii::t('backend', 'Добавить'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
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
            'description:ntext',

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
</div>
