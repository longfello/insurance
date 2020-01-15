<?php
/* @var $this yii\web\View */
/* @var $model common\models\WidgetText */

$this->title = 'Добавление текстового блока';
$this->params['breadcrumbs'][] = ['label' => 'Текстовые блоки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="text-block-create">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
