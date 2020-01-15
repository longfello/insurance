<?php
/* @var $this yii\web\View */
/* @var $model common\modules\ApiRgs\models\Classifier */

$this->title = 'Добавление справочника';
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="classifier-create">

    <?php
    echo $this->render('_form', [
        'model' => $model,
    ]);
    ?>

</div>
