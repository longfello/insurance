<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Risk */

$this->title = Yii::t('backend', 'Добавить');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Риски'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="risk-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
