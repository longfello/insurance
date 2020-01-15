<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Опции по медицине';
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
                $l_model = \common\modules\ApiLiberty\models\Occupation2Product::find()->where(['productId' => $product->productId, 'id' => $model->id])->one();
                return ($l_model)?"+":"-";
            }
        ];
    }

    ?>

    <p>
        <?= Html::a(Yii::t('backend', 'Обновить список опций'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'occupationName',
                [
                    'label' => 'Занятия спортом (внутр.)',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'value' => function($model){
                        $html = Html::beginForm('/liberty-occupation/save-sport', 'post', ['style' => 'width:50px']);
                        $html .= \yii\bootstrap\Html::hiddenInput('occupationId', $model->id);
                        $html .= \yii\bootstrap\Html::checkbox('checked', (bool)$model->is_sport, [ 'class' => 'ajaxify' ]);
                        $html .= Html::endForm();
                        return $html;
                    }
                ]
        ] + $productsFields +['functions' => ['class' => 'yii\grid\ActionColumn','template' => '']],
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
