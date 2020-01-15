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

$this->title = 'Страны';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-import">
  <div class="well">
	  <p>Обновлено стран: <?= $updated?></p>
	  <p>Добавлено стран: <?= $inserted?></p>
    <p>Удалено стран: <?= $deleted?></p>
  </div>
</div>
