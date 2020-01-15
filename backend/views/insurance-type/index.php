<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Типы страхования';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="insurance-type-index">
    <p>
        <?= Html::a('Добавить тип страхования', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'id',
            'slug',
            'name',
//            'description',
//            'class',
            'calc_page_id' => [
	            'attribute' => 'calc_page_id',
              'format' => 'html',
	            'value' => function(\common\models\InsuranceType $model){
		            return $model->calcPage?Html::a($model->calcPage->title, $model->calcPage->createUrl(), ['target'=>'_blank']):" <span class='badge label-danger'>не задано</span> ";
	            }
            ],
            'result_page_id' => [
	            'attribute' => 'result_page_id',
              'format' => 'html',
	            'value' => function(\common\models\InsuranceType $model){
		            return $model->resultPage?Html::a($model->resultPage->title, $model->resultPage->createUrl(), ['target'=>'_blank']):" <span class='badge label-danger'>не задано</span> ";
	            }
            ],
            'program_page_id' => [
	            'attribute' => 'program_page_id',
              'format' => 'html',
	            'value' => function(\common\models\InsuranceType $model){
		            return $model->programPage?Html::a($model->programPage->title, $model->programPage->createUrl(), ['target'=>'_blank']):" <span class='badge label-danger'>не задано</span> ";
	            }
            ],
            'about_page_id' => [
	            'attribute' => 'about_page_id',
              'format' => 'html',
	            'value' => function(\common\models\InsuranceType $model){
		            return $model->aboutPage?Html::a($model->aboutPage->title, $model->aboutPage->createUrl(), ['target'=>'_blank']):" <span class='badge label-danger'>не задано</span> ";
	            }
            ],
            'landing_page_id' => [
                'attribute' => 'landing_page_id',
                'format' => 'html',
                'value' => function(\common\models\InsuranceType $model){
                    return $model->landingPage?Html::a($model->landingPage->title, $model->landingPage->createUrl(), ['target'=>'_blank']):" <span class='badge label-danger'>не задано</span> ";
                }
            ],
            'sort_order',
            'enabled' => [
	            'attribute' => 'enabled',
	            'format' => 'html',
	            'value' => function(\common\models\InsuranceType $model){
	              return $model->enabled?" <span class='badge label-success'>разрешен</span> ":" <span class='badge label-danger'>запрещен</span> ";
	            }
            ],
            'active' => [
	            'attribute' => 'active',
	            'format' => 'html',
	            'value' => function(\common\models\InsuranceType $model){
	              return $model->active?" <span class='badge label-success'>активен</span> ":" <span class='badge label-danger'>пассивен</span> ";
	            }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
