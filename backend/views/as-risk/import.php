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

$this->title = 'Риски';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-import">
  <div class="well">
	  <p>Обновлено рисков: <?= $updated?></p>
	  <p>Добавлено рисков: <?= $inserted?></p>
    <p>Удалено рисков: <?= $deleted?></p>
  </div>
</div>
