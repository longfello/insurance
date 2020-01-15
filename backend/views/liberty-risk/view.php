<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use common\modules\ApiLiberty\models\Summ;
use common\modules\ApiLiberty\models\Summ2Interval;
use common\modules\ApiLiberty\models\Risk2Internal;

/* @var $this yii\web\View */
/* @var $risk_model \common\modules\ApiLiberty\models\Risk */
/* @var $summDataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */

$this->title = $risk_model->riskName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Риски'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="risk-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">Основная информация</a></li>
        <li><a href="#tab2" data-toggle="tab">Внутренние риски</a></li>
    </ul>
    <div class="tab-content ">
    <div class="tab-pane active" id="tab1">
        <?= DetailView::widget([
            'model' => $risk_model,
            'attributes' => [
                'riskId',
                'riskName',
                [
                    'format' => 'raw',
                    'label' => 'Основной риск',
                    'value' => function($model){
                        return ($model->main==1)?'да':'нет';
                    }
                ]
            ],
        ]) ?>
        <?php
        if ($risk_model->main==0) {
            $form = ActiveForm::begin(); ?>

            <?= $form->field($risk_model, 'description')->textarea(['rows' => 6]) ?>

            <div class="form-group">
                <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <p><i class="icon fa fa-check"></i><?= Yii::$app->session->getFlash('success') ?></p>
                </div>
            <?php endif; ?>
            <?php ActiveForm::end();
        }?>

    <?php

    $costFields = [];
    if($risk_model->main==0) {
        $costs = \common\models\CostInterval::find()->orderBy(['id' => SORT_ASC])->all();
        foreach ($costs as $cost) {
            $costFields['cost_' . $cost->id] = [
                'label' => Html::tag('span', $cost->name),
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function ($model) use ($cost) {
                    $l_model = Summ2Interval::find()->where(['cost_id' => $cost->id, 'summ_id' => $model->id])->one();

                    $html = Html::beginForm('/liberty-risk/save-cost', 'post', ['style' => 'width:50px']);
                    $html .= \yii\bootstrap\Html::hiddenInput('riskId', $model->riskId);
                    $html .= \yii\bootstrap\Html::hiddenInput('productId', $model->productId);
                    $html .= \yii\bootstrap\Html::hiddenInput('amount', $model->amount);
                    $html .= \yii\bootstrap\Html::hiddenInput('cost_id', $cost->id);
                    $html .= \yii\bootstrap\Html::checkbox('checked', (bool)$l_model, ['class' => 'ajaxify']);
                    $html .= Html::endForm();
                    return $html;
                }
            ];
        }
    }

    ?>
    <h2>Страховые суммы <?= Html::a(Yii::t('backend', 'Обновить'), ['import-summ','riskId'=>$risk_model['riskId']], ['class' => 'badge btn-warning']) ?></h2>
    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $summDataProvider,
        'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'productId'  => [
                        'attribute' => 'productId',
                        'label' => 'Продукт',
                        'value' => function($model){
                            return $model->product->productName." (".$model->productId.")";
                        }
                     ],
                    'amount',
                    'col_countries' => [
                        'label' => 'Территорий',
                        'value' => function($model){
                            $res =  Summ::find()
                                ->select(['COUNT(*) AS cnt'])
                                ->where(['riskId' => $model->riskId, 'productId'=>$model->productId, 'amount'=>$model->amount])
                                ->createCommand()->queryOne();
                            return isset($res['cnt'])?$res['cnt']:0;
                        }
                    ],
                ] + $costFields + ['functions' => [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => ($risk_model->main==1)?'{view_summ} {update_summ}':'{view_summ}',
                         'buttons'=>[
                            'view_summ'=>function ($url, $model) {
                                $customurl=Yii::$app->getUrlManager()->createUrl(['liberty-risk/view-territories','riskId'=>$model['riskId'], 'productId'=>$model->productId, 'amount'=>$model->amount]); //$model->id для AR
                                return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-eye-open"></span>', $customurl,
                                    ['title' => Yii::t('yii', 'View'), 'data-pjax' => '0']);
                            },
                            'update_summ'=>function ($url, $model) {
                                 $customurl=Yii::$app->getUrlManager()->createUrl(['liberty-risk/update-summ','riskId'=>$model['riskId'], 'productId'=>$model->productId, 'amount'=>$model->amount]); //$model->id для AR
                                 return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-edit"></span>', $customurl,
                                     ['title' => Yii::t('yii', 'Update'), 'data-pjax' => '0']);
                            }
                        ],
                    ]
        ],
    ]); ?>
    </div>
    <div class="tab-pane" id="tab2">
    <?php $form = ActiveForm::begin(); ?>
    <?php echo $form->errorSummary($risk_model); ?>
    <?php
    $risks = \common\models\Risk::find()->orderBy(['name' => SORT_ASC])->all();
    foreach ($risks as $risk){
        /** @var $risk \common\models\Risk */
        ?>

        <div class="col-xs-6">
            <label for="risk-<?=$risk->id ?>">
                <input id="risk-<?=$risk->id ?>" name="Risk2internal[]" value="<?= $risk->id ?>" type="checkbox" <?php if (Risk2internal::findOne(['riskId' => $risk_model->riskId, 'internal_id' => $risk->id])){ echo('checked'); } ?>>
                <?=$risk->name ?>
            </label>
        </div>
        <?php
    }
    ?>
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    </div>


</div>
<?php

$this->registerJs("
  $(document).on('change', '.ajaxify', function(){

    var form = $(this).parents('form');
    $(form).blur();
    var data = $(form).serializeArray();

    $.post($(form).attr('action'), data, function(){

    });

  });
");