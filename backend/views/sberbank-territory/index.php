<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Территории';
$this->params['breadcrumbs'][] = $this->title;

$products = \common\modules\ApiLiberty\models\Product::find()->orderBy(['productId' => SORT_ASC])->all();

?>
    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<div class="country-index">
    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'insTerritory',
            'name',
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
                ],
            'functions' => ['class' => 'yii\grid\ActionColumn','template' => '{update}{delete}']],
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
