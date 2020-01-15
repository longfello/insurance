<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Програмы страхования';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-index">

    <p>
        <?php echo Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'code',
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{clone} {update} {delete}',
              'buttons' =>[
	              'clone' => function ($url, $model) {
		              /** @var $model \common\modules\ApiVtb\models\Price */
		              return Html::a( '<span class="glyphicon glyphicon-duplicate"></span>', ['/vtb-program/clone', 'id' => $model->id], [
			              'title' => 'Клонировать',
		              ] );
	              },
	              'update' => function ($url, $model) {
		              /** @var $model \common\modules\ApiVtb\models\Price */
		              return Html::a( '<span class="glyphicon glyphicon-pencil"></span>', ['/vtb-program/update', 'id' => $model->id], [
			              'title' => 'Редактировать',
		              ] );
	              },
	              'delete' => function ($url, $model) {
		              /** @var $model \common\modules\ApiVtb\models\Price */
		              return Html::a( '<span class="glyphicon glyphicon-trash"></span>', ['/vtb-program/delete', 'id' => $model->id], [
			              'title' => 'Удалить',
			              'aria-label' => Yii::t('yii', 'Удалить'),
			              'data-confirm' => Yii::t('yii', 'Вы уверены, что хотите удалить этот элемент?'),
			              'data-method' => 'post',
			              'data-pjax' => '0',
		              ] );
	              },

              ],
            ],
        ],
    ]); ?>

</div>
