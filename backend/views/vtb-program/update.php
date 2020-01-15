<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Program */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'Редактирование програмы страхования: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = [ 'label' => 'Програмы страхования', 'url' => [ 'index' ] ];
$this->params['breadcrumbs'][] = [ 'label' => $model->name, 'url' => [ 'view', 'id' => $model->id ] ];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="program-update">

	<?php echo $this->render( '_form_update', [
		'model' => $model,
	] ) ?>

  <h5>Страховые суммы <?php echo Html::a('Добавить страховую сумму', ['/vtb-price/create', 'program_id' => $model->id], ['class' => 'btn btn-success pull-right']) ?></h5>
  <div class="clearfix"></div>

	<?php echo \kartik\grid\GridView::widget( [
		'dataProvider' => $dataProvider,
		'columns'      => [
			[ 'class' => 'yii\grid\SerialColumn' ],
      'id',
      'region_id' => [
        'attribute' => 'region_id',
        'value'      => function ( $model ) {
          /** @var $model \common\modules\ApiVtb\models\Price */
          return $model->region->name;
        }
      ],
			'amount_id' => [
				'attribute' => 'amount_id',
				'value'      => function ( $model ) {
					/** @var $model \common\modules\ApiVtb\models\Price */
					return $model->amount->amount;
				}
			],
			'period_id' => [
				'attribute' => 'period_id',
				'value'      => function ( $model ) {
					/** @var $model \common\modules\ApiVtb\models\Price */
					return $model->period->asText;
				}
			],
			'price',
			[
				'class'    => 'yii\grid\ActionColumn',
				'template' => '{clone} {update} {delete}',
        'buttons' =>[
          'clone' => function ($url, $model) {
            /** @var $model \common\modules\ApiVtb\models\Price */
            return Html::a( '<span class="glyphicon glyphicon-duplicate"></span>', ['/vtb-price/clone', 'program_id'=>$model->program_id, 'id' => $model->id], [
              'title' => 'Клонировать',
            ] );
          },
          'update' => function ($url, $model) {
            /** @var $model \common\modules\ApiVtb\models\Price */
            return Html::a( '<span class="glyphicon glyphicon-pencil"></span>', ['/vtb-price/update', 'program_id'=>$model->program_id, 'id' => $model->id], [
              'title' => 'Редактировать',
            ] );
          },
          'delete' => function ($url, $model) {
            /** @var $model \common\modules\ApiVtb\models\Price */
            return Html::a( '<span class="glyphicon glyphicon-trash"></span>', ['/vtb-price/delete', 'program_id'=>$model->program_id, 'id' => $model->id], [
              'title' => 'Удалить',
              'aria-label' => Yii::t('yii', 'Удалить'),
              'data-confirm' => Yii::t('yii', 'Вы уверены, что хотите удалить этот элемент?'),
              'data-method' => 'post',
              'data-pjax' => '0',
            ] );
          },
        ]
			],
		],
	] ); ?>

</div>
