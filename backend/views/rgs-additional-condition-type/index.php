<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\modules\ApiRgs\models\AdditionalConditionTypeRisk;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Виды дополнительных условий';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="additional-condition-type-index">
    <p>
        <?= Html::a(Yii::t('backend', 'Обновить виды доп. условий'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'title',
            [
                'format' => 'raw',
                'label' => 'Соответствие внутреннему справочнику рисков',
                'value' => function($model) {
                    $risks = AdditionalConditionTypeRisk::find()->where(['additional_condition_type_id' => $model->id])->all();
                    $res = [];
                    foreach ($risks as $risk) {
                        /** @var $risk common\modules\ApiRgs\models\AdditionalConditionTypeRisk */
                        $res[] = $risk->riskModel->name;
                    }
                    sort($res);
                    return Html::ul($res);
                }
            ],
            'functions' => [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}'
            ]
        ]
    ]);
    ?>
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
