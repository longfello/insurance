<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Orders */

$this->title = 'Заказ #'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$view = $this;
?>
<div class="orders-view">

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'api_id' => [
              'attribute' => 'api_id',
              'value'     => function(\common\models\Orders $model){
                return $model->api->name;
              }
            ],
            'price' => [
              'attribute' => 'price',
              'value'     => function(\common\models\Orders $model){
                return $model->currency->char_code.' '.$model->price;
              }
            ],
            'status' => [
              'attribute' => 'status',
              'value' => function(\common\models\Orders $model){
                return $model->statusName;
              }
            ],
            'holder_id' => [
              'attribute' => 'holder_id',
              'value'     => function(\common\models\Orders $model){
	              return $model->holder->first_name.' '.$model->holder->last_name . ' ('.$model->holder->email.')';
              }
            ],
            'calc_form' => [
              'attribute' => 'calc_form',
              'format'    => 'html',
              'value'     => function(\common\models\Orders $model) use ($view){
	              return $view->render('_calc_form', ['model' => $model]);
              }
            ],
            'program' => [
              'attribute' => 'program',
              'format'    => 'html',
              'value'     => function(\common\models\Orders $model){
	              if (method_exists($model->program, 'preview')) return $model->program->preview();
	              if (isset($model->program->name)) return  $model->program->name;
	              return '?';
              }
            ],
            'info' => [
              'attribute' => 'info',
              'format' => 'html',
              'value' => function(\common\models\Orders $model){
	              return '<pre class="col-xs-12">'.yii\helpers\VarDumper::dumpAsString($model->info, 20, true).'</pre>';
              }
            ],
            'police' => [
              'label' => 'Полис',
              'format' => 'html',
                'value' => function(\common\models\Orders $model){
                    if ($model->status == \common\models\Orders::STATUS_PAYED) {
                        if ($model->info && is_array($model->info) && isset($model->info['mode']) && $model->info['mode'] == 'test'){
                            ?> Заказ создан в тестовом режиме <?php
                        } else {
                            $link = \yii\helpers\Url::to($model->getPoliceLink(), true);
                            return \yii\bootstrap\Html::a($link, $link).\yii\bootstrap\Html::a('Пересоздать', \yii\helpers\Url::to(['refresh', 'id'=>$model->id]), ['class' => 'btn btn-danger pull-right']);
                        }
                    }
                }
            ],
            'created_at',
        ],
    ]) ?>

</div>
