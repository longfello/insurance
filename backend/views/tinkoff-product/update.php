<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiTinkoff\models\Product */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'Редактирование продукта: ' . ' ' . $model->Name;
$this->params['breadcrumbs'][] = [ 'label' => 'Продукты', 'url' => [ 'index' ] ];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
    <div class="product-update">

    <?php echo $this->render( '_form', [
        'model' => $model
    ] ) ?>
        <h5>Страховые суммы <?php echo Html::a('Добавить страховую сумму', ['/tinkoff-price/create', 'product_id' => $model->id], ['class' => 'btn btn-success pull-right']) ?></h5>
        <div class="clearfix"></div>
        <?php echo \kartik\grid\GridView::widget( [
            'dataProvider' => $dataProvider,
            'columns'      => [
                [ 'class' => 'yii\grid\SerialColumn' ],
                'name',
                'AssistanceLevel',
                'Currency',
                'TravelMedicineLimit',
                'DeductibleAmount',
                [
                    'class'    => 'yii\grid\ActionColumn',
                    'template' => '{clone} {update} {delete}',
                    'buttons' =>[
                        'clone' => function ($url, $model) {
                            /** @var $model \common\modules\ApiTinkoff\models\Price */
                            return Html::a( '<span class="glyphicon glyphicon-duplicate"></span>', ['/tinkoff-price/clone', 'product_id'=>$model->product_id, 'id' => $model->id], [
                                'title' => 'Клонировать',
                            ] );
                        },
                        'update' => function ($url, $model) {
                            /** @var $model \common\modules\ApiTinkoff\models\Price */
                            return Html::a( '<span class="glyphicon glyphicon-pencil"></span>', ['/tinkoff-price/update', 'product_id'=>$model->product_id, 'id' => $model->id], [
                                'title' => 'Редактировать',
                            ] );
                        },
                        'delete' => function ($url, $model) {
                            /** @var $model \common\modules\ApiTinkoff\models\Price */
                            return Html::a( '<span class="glyphicon glyphicon-trash"></span>', ['/tinkoff-price/delete', 'product_id'=>$model->product_id, 'id' => $model->id], [
                                'title' => 'Удалить',
                                'aria-label' => Yii::t('yii', 'Удалить'),
                                'data-confirm' => Yii::t('yii', 'Вы уверены, что хотите удалить этот элемент?'),
                                'data-method' => 'post',
                                'data-pjax' => '0',
                            ] );
                        },
                    ]
                ]
            ],
        ] ); ?>
    </div>
