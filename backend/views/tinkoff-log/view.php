<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\SystemLog */

$this->title = Yii::t('backend', '#{id}', ['id'=>$model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Журнал обращений к API'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-log-view">

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'log_time',
                'format' => 'datetime',
                'value' => (int) $model->log_time
            ],
            'prefix:ntext',
            [
                'attribute'=>'message',
                'format'=>'raw',
                'value'=>Html::tag('pre', $model->message, ['style'=>'white-space: pre-wrap'])
            ],
        ],
    ]) ?>

</div>
