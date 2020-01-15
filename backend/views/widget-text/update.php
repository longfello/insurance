<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WidgetText */

$this->title = 'Редактирование текстового блока: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Текстовые блоки', 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="text-block-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
