<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Риски';
$this->params['breadcrumbs'][] = $this->title;

$products = \common\modules\ApiTinkoff\models\Product::find()->orderBy(['id' => SORT_ASC])->all();

?>
<div class="country-index">
    <?php
    $productsFields = [];
    foreach ($products as $product){
        $productsFields['product_'.$product->id] = [
            'label' => Html::tag('span', $product->Name),
            'encodeLabel' => false,
            'format' => 'raw',
            'value' => function($model) use ($product) {
                $l_model = \common\modules\ApiTinkoff\models\Risk2Product::find()->where(['product_id' => $product->id, 'risk_id' => $model->id])->one();
                return ($l_model)?"+":"-";
            }
        ];
    }

    ?>
    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'Name',
            'Code',
                [
                    'class'=>\kartik\grid\EditableColumn::className(),
                    'attribute'=>'enabled',
                    'pageSummary'=>true,
                    'editableOptions'=> function ($model, $key, $index) {
                        return [
                            'size'   => \kartik\popover\PopoverX::SIZE_MEDIUM,
                            'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                            'data' => [
                                0 => 'Запрещен',
                                1 => 'Разрешен'
                            ],
                            'displayValueConfig' => [
                                0 => 'Запрещен',
                                1 => 'Разрешен'
                            ],
                        ];
                    }
                ],
                [
                    'format' => 'raw',
                    'label' => 'Соответствие внутреннему справочнику',
                    'value' => function($model){
                        $risks = \common\modules\ApiTinkoff\models\Risk2Internal::find()->where(['risk_id' => $model->id])->all();
                        $res = [];
                        foreach ($risks as $one_risk){
                            $res[] = $one_risk->internalRiskModel->name;
                        }
                        sort($res);
                        return Html::ul($res);
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
