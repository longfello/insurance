<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 23.12.16
 * Time: 14:58
 */
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Дополнительные условия';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-import">
  <div class="well">
	  <p>Обновлено условий: <?= $updated?></p>
	  <p>Добавлено условий: <?= $inserted?></p>
    <p>Удалено условий: <?= $deleted?></p>
  </div>
</div>
