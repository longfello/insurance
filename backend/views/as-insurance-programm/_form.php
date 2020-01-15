<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\modules\ApiVtb\models\Program */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="program-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>
    <ul class="nav nav-tabs">
        <li class="active"><a  href="#tab1" data-toggle="tab">Основная информация</a></li>
        <li><a href="#tab2" data-toggle="tab">Риски</a></li>
    </ul>
    <div class="tab-content ">
        <div class="tab-pane active" id="tab1">
    <?php echo $form->field($model, 'insuranceProgrammName')->textInput(['maxlength' => true]) ?>

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
    <?= $form->field($model, 'pregnant_week')->dropDownList(\yii\helpers\ArrayHelper::map(
        $model->getPregnantVariants(),
        'id',
        'name'
    )) ?>
            <div class="form-group">
                <?php echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <div class="tab-pane" id="tab2">
            <?= GridView::widget([
                'dataProvider' => $dataRiskProvider,
                'columns' => [
                    'risk_id',
                    [
                        'attribute' => 'Название риска',
                        'filter' => false,
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->getRisk()->one()->risk;
                        },
                    ],
                    [
                        'attribute' => 'Внутренние риски',
                        'filter' => false,
                        'format' => 'raw',
                        'value' => function ($model) {
                            $irisks = $model->getInternalRisks();
                            $res = "<ul>";
                            foreach ($irisks as $irisk) {
                                $res .= "<li>".$irisk->name."</li>";
                            }
                            $res .= "</ul>";
                            return $res;
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{edit}',
                        'buttons' => [
                                'edit' => function ($url,$model) {
                                        return Html::a(
                                            '<span class="glyphicon glyphicon-edit"></span>',
                                            $url);
                                }
                        ],
                    ]
                ],
            ]); ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
