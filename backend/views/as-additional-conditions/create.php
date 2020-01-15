<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model \common\modules\ApiAlphaStrah\models\AdditionalConditions */

$this->title = 'Добавление дополнительного условия';
$this->params['breadcrumbs'][] = ['label' => 'Дополнительные условия', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="additional-condition-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
