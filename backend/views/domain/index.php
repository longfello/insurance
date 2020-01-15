<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Домены';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="domain-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить домен', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'city_id' => [
              'attribute' => 'city_id',
              'value' => function($model){
                  if ($model->city){
                    return $model->city->name;
                  }
                  return ' (не задано) ';
              }
            ],
            'country_id' => [
              'attribute' => 'country_id',
              'value' => function($model){
                  if ($model->country){
                    return $model->country->name;
                  }
                  return ' (не задано) ';
              }
            ],
            'default' => [
              'attribute' => 'default',
              'format'    => 'raw',
              'value'     => function($model){
                  if ($model->default) {
                    return '<div class="btn btn-xs btn-success">Основной</div>';
                  }
                  return '';
              }
            ],
            'description',
            'enabled' => [
                'attribute' => 'enabled',
                'format'    => 'raw',
                'value'     => function($model){
                    if ($model->enabled) {
                        return '<div class="btn btn-xs btn-success">Разрешен</div>';
                    } else {
                        return '<div class="btn btn-xs btn-danger">Запрещен</div>';
                    }
                }
            ],
            'default_language' => [
              'attribute' => 'default_language',
              'value' => function($model){
                  /** @var $model \common\models\Domain */
                  $default = '(не задано) ';
                  if($model->defaultLanguage){
                      $default = $model->defaultLanguage->name.' ';
                  }

                  $langs = [];
                  foreach ($model->languages as $one){
                    $langs[] = $one->name;
                  }
                  $langs = implode(',', $langs);

                  if ($langs) {
                      $ret = $default."({$langs})";
                  } else {
                      $ret = $default."<span class='text-danger'>(языки не заданы)</span>";
                  }

                  return $ret;
              }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
