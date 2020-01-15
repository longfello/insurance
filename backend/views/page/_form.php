<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Page */
/* @var $form yii\bootstrap\ActiveForm */

$dir = Yii::getAlias('@frontend').'/views/page';
$template_files = glob($dir.'/*.php');

$templates = [];
$view = trim($model->view);
$templates[$view] = [$view];
foreach ($template_files as $template){
	$view = trim(basename($template, '.php'));
	$title = $view;
  $content = file($template);
  foreach ($content as $line){
    if (strpos($line, '@title')){
	    $title = str_replace('@title', '', $line) . " - $title";
	    $title = str_replace('*', '', $title);
	    $title = trim($title);
      break;
    }
  }
  $templates[$view] = $title;
}
ksort($templates);

?>

<div class="page-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?php if ($model->view == 'plainWithEditor'){ ?>
        <?php echo $form->field($model, 'body')->widget(      \yii\imperavi\Widget::className(),
            [
                'plugins' => ['fullscreen', 'fontcolor', 'inlinestyle'],
                'options' => [
                    'formatting' => ['h2', 'h3', 'h4', 'h5', 'p', 'pre', 'blockquote'],
                    'minHeight' => 400,
                    'maxHeight' => 400,
                    'buttonSource' => true,
                    'convertDivs' => false,
                    'removeEmptyTags' => false,
                ]
            ]
        ) ?>
    <?php } else { ?>
        <?php echo $form->field($model, 'body')->textarea(['rows' => 20]) ?>
    <?php } ?>
    <?php echo $form->field($model, 'view')->dropDownList($templates) ?>

    <?php echo $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
