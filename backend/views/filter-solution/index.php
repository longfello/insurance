<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Готовые решения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="filter-solution-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать готовое решение', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            'description',
            'is_front' => [
                'attribute' => 'is_front',
                'value' => function($model){
                    return ($model->is_front==1)?'Да':'Нет';
                }
            ],
            'is_api' => [
                'attribute' => 'is_api',
                'value' => function($model){
                    return ($model->is_api==1)?'Да':'Нет';
                }
            ],
            'apis' => [
                'label' => 'Api для рассчета',
                'format' => 'html',
                'value' => function($model){
                    if (count($model->api)){
                        $res = '<ul>';
                        foreach ($model->api as $one_api) {
                            $res.="<li>".$one_api->name."</li>";
                        }
                        $res .= '</ul>';

                    } else $res = '';
                    return $res;
                }
            ],
	        [
		        'class' => 'yii\grid\ActionColumn',
		        'template' => '{update} {delete}'
	        ],
        ],
    ]); ?>
</div>
