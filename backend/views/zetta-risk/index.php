<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\modules\ApiZetta\models\Risk2dict;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Риски';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="country-index">
    <p>
        <?= Html::a(Yii::t('backend', 'Обновить список рисков'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'title',
            [
                'format' => 'raw',
                'label' => 'Соответствие внутреннему справочнику',
                'value' => function($model) {
                    $risks = Risk2dict::find()->where(['risk_id' => $model->id])->all();
                    $res = [];
                    foreach ($risks as $risk) {
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
