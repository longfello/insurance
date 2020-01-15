<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model common\modules\ApiTinkoff\models\Product */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="program-form">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'Name',
            'ProductType',
            'ProductVersion',
            'ShortDescription',
            'FullDescription',
            [
                'format' => 'raw',
                'label' => 'Уровни поддержки',
                'value' => function($model){
                    $result = "<ul>";
                    foreach ($model->AssistanceLevel as $val) {
                        $result .= "<li>".$val['Display']."</li>";
                    }
                    $result .= "</ul>";
                    return $result;
                }
            ],
            [
                'format' => 'raw',
                'label' => 'Доступные валюты',
                'value' => function($model){
                    $result = "<ul>";
                    foreach ($model->Currency as $val) {
                        $result .= "<li>".$val['Display']."</li>";
                    }
                    $result .= "</ul>";
                    return $result;
                }
            ]
        ],
    ]) ?>
    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'rule')->widget(
        \trntv\filekit\widget\Upload::className(),
        [
            'url' => ['/file-storage/upload'],
            'maxFileSize' => 5000000, // 5 MiB
        ]);
    ?>
    <?php echo $form->field($model, 'police')->widget(
        \trntv\filekit\widget\Upload::className(),
        [
            'url' => ['/file-storage/upload'],
            'maxFileSize' => 5000000, // 5 MiB
        ]);
    ?>
    <div class="form-group">
        <?php echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>