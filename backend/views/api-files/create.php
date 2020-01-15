<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ApiPhone */

$this->title = 'Добавить файл';
$this->params['breadcrumbs'][] = ['label' => 'Api', 'url' => ['/api/index']];;
$this->params['breadcrumbs'][] = ['label' => 'Редактирование', 'url' => ['/api/update', 'id' => $model->api_id]];;
$this->params['breadcrumbs'][] = ['label' => 'Файлы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-files-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
