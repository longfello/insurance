<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\modules\ApiAlphaStrah\models\AdditionalConditions */

$this->title = 'Редактирование дополнительного условия: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Дополнительные условия', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="additional-condition-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
