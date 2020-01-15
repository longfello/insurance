<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiAlphaStrah\models\InsuranceProgramm */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'Редактирование програмы страхования: ' . ' ' . $model->insuranceProgrammName;
$this->params['breadcrumbs'][] = [ 'label' => 'Програмы страхования', 'url' => [ 'index' ] ];
$this->params['breadcrumbs'][] = [ 'label' => $model->insuranceProgrammName, 'url' => [ 'view', 'id' => $model->insuranceProgrammID ] ];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="program-update">

	<?php echo $this->render( '_form', [
		'model' => $model,
		'dataRiskProvider' => $dataRiskProvider,
	] ) ?>

  <h5>Страховые суммы <?php echo Html::a('Добавить страховую сумму', ['/as-price/create', 'program_id' => $model->insuranceProgrammID], ['class' => 'btn btn-success pull-right']) ?></h5>
  <div class="clearfix"></div>

	<?php echo \kartik\grid\GridView::widget( [
		'dataProvider' => $dataProvider,
		'columns'      => [
			[ 'class' => 'yii\grid\SerialColumn' ],
      'region_id' => [
        'attribute' => 'region_id',
        'value'      => function ( $model ) {
          /** @var $model \common\modules\ApiAlphaStrah\models\Price */
          return $model->region->name;
        }
      ],
			'amount_id' => [
				'attribute' => 'amount_id',
				'value'      => function ( $model ) {
					/** @var $model \common\modules\ApiAlphaStrah\models\Price */
					return $model->amount->amount;
				}
			],
			'price',
			[
				'class'    => 'yii\grid\ActionColumn',
				'template' => '{update} {delete}',
        'buttons' =>[
          'update' => function ($url, $model) {
            /** @var $model \common\modules\ApiAlphaStrah\models\Price */
            return Html::a( '<span class="glyphicon glyphicon-pencil"></span>', ['/as-price/update', 'program_id'=>$model->program_id, 'id' => $model->id], [
              'title' => 'Редактировать',
            ] );
          },
          'delete' => function ($url, $model) {
            /** @var $model \common\modules\ApiAlphaStrah\models\Price */
            return Html::a( '<span class="glyphicon glyphicon-trash"></span>', ['/as-price/delete', 'program_id'=>$model->program_id, 'id' => $model->id], [
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
