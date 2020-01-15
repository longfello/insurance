<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Landing */

$this->title = Yii::t('backend', 'Update {modelClass}: ', [
    'modelClass' => 'Landing',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Landings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="Landing-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
