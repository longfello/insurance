<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Риски';
$this->params['breadcrumbs'][] = $this->title;

$products = \common\modules\ApiLiberty\models\Product::find()->orderBy(['productId' => SORT_ASC])->all();

?>
<div class="country-index">
    <?php
    $productsFields = [];
    foreach ($products as $product){
        $productsFields['product_'.$product->productId] = [
            'label' => Html::tag('span', $product->productName),
            'encodeLabel' => false,
            'format' => 'raw',
            'value' => function($model) use ($product) {
                $l_model = \common\modules\ApiLiberty\models\Risk2Product::find()->where(['productId' => $product->productId, 'riskId' => $model->riskId])->one();
                return ($l_model)?"+ ".($l_model->required?'(обяз.)':''):"-";
            }
        ];
    }

    ?>

    <p>
        <?= Html::a(Yii::t('backend', 'Обновить список рисков'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'riskId',
            'riskName',
                [
                    'format' => 'raw',
                    'label' => 'Соответствие внутреннему справочнику',
                    'value' => function($model){
                        $risks = \common\modules\ApiLiberty\models\Risk2Internal::find()->where(['riskId' => $model->riskId])->all();
                        $res = [];
                        foreach ($risks as $one_risk){
                            $res[] = $one_risk->internalRiskModel->name;
                        }
                        sort($res);
                        return Html::ul($res);
                    }
                ],
                [
                    'label' => 'Основной риск (мед.)',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'value' => function($model){
                        $html = Html::beginForm('/liberty-risk/save-risk-main', 'post', ['style' => 'width:50px']);
                        $html .= \yii\bootstrap\Html::hiddenInput('riskId', $model->riskId);
                        $html .= \yii\bootstrap\Html::checkbox('checked', (bool)$model->main, [ 'class' => 'ajaxify' ]);
                        $html .= Html::endForm();
                        return $html;
                    }
                ]
        ] + $productsFields + ['functions' => ['class' => 'yii\grid\ActionColumn','template' => '{view}']],
    ]); ?>
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
