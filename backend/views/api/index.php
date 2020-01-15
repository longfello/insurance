<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Api';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-index">
    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'thumbnail' => [
              'attribute' => 'thumbnail',
              'format' => 'html',
              'value' => function($model){
                  if ($model->thumbnail_path){
                    return \yii\helpers\Html::img(
                      Yii::$app->glide->createSignedUrl([
                        'glide/index',
                        'path' => $model->thumbnail_path,
                        'w' => 200
                      ], true),
                      ['class' => 'article-thumb img-rounded pull-left']
                    );
                  }
                  return '- ';
//                    return print_r($model->thumbnail, true);
              }
            ],
            'name',
            'class',
            'rate_expert',
            'rate_asn',
            'enabled' => [
              'attribute' => 'enabled',
              'value' => function($model){ return $model->enabled?"Разрешено":"Запрещено"; }
            ],
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
</div>
