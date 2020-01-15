<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Территории';
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
                $l_model = \common\modules\ApiLiberty\models\Territory2Product::find()->where(['productId' => $product->productId, 'id_area' => $model->id_area])->one();
                return ($l_model)?"+":"-";
            }
        ];
    }

    ?>

    <p>
        <?= Html::a(Yii::t('backend', 'Обновить список территорий'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id_area',
            'name',
               [
                 'format' => 'raw',
                 'label' => 'Соответствие внутреннему справочнику',
                 'value' => function($model){
                   $countries = \common\modules\ApiLiberty\models\Territory2Dict::find()->where(['id_area' => $model->id_area])->all();
                   $res = [];
                   foreach ($countries as $country){
                        $res[] = $country->geoNameModel->name;
                   }
                   sort($res);
                   return Html::ul($res);
                 }
               ],
            'territoryGroupId',
                [
                    'class'=>\kartik\grid\EditableColumn::className(),
                    'attribute'=>'enabled',
                    'pageSummary'=>true,
                    'editableOptions'=> function ($model, $key, $index) {
                        return [
                            'size'   => \kartik\popover\PopoverX::SIZE_MEDIUM,
                            'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                            'data' => [
                                0 => 'Запрещена',
                                1 => 'Разрешена'
                            ],
                            'displayValueConfig' => [
                                0 => 'Запрещена',
                                1 => 'Разрешена'
                            ],
                        ];
                    }
                ]
        ] + $productsFields + ['functions' => ['class' => 'yii\grid\ActionColumn','template' => '{update}']],
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
