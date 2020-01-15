<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AdditionalCondition */

$this->title = Yii::t('backend', 'Добавить');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Дополнительные условия'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="additional-condition-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
