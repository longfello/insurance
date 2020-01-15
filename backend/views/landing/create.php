<?php
/* @var $this yii\web\View */
/* @var $model common\models\Landing */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Landing',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Landings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="Landing-create">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
