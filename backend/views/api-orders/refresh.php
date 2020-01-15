<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Orders */

$this->title = 'Заказ #'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Обновление заказа';
$view = $this;
?>

<table class="table table-bordered table-hover table-striped">
	<tr>
		<th>Время</th>
		<th>Сообщение</th>
	</tr>
	<?php foreach ($log as $time => $message) { ?>
	<tr>
		<td><?= date('d.m.Y H:i:s', $time) ?></td>
		<td><?= $message ?></td>
	</tr>
	<?php } ?>
</table>

<a href="<?= \yii\helpers\Url::to(['view', 'id' => $model->id]) ?>" class="btn btn-primary">Вернутся к заказу</a>