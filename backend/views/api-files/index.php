<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $api_id integer */
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Файлы страховой компании';
$this->params['breadcrumbs'][] = ['label' => 'Api', 'url' => ['/api/index']];;
$this->params['breadcrumbs'][] = ['label' => 'редактирование', 'url' => ['/api/update', 'id' => $api_id]];;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-files-index">
    <p>
        <?= Html::a('Добавить файл', ['create', 'api_id' => $api_id], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'file' => [
              'attribute' => 'file_base_url',
              'format' => 'raw',
              'value' => function($model){
                  /** @var $model \common\models\ApiFiles */
  	              $link = $model->file_base_url.'/'.$model->file_path;
                  return Html::a($link, $link, ['target' => '_blank']);
              }
            ],
            [
              'class'    => 'yii\grid\ActionColumn',
              'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
</div>
