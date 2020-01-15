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

$this->title = Yii::t('backend', 'Продукты');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-import">
  <div class="well">
    <p>Обработано: <?= $processed?></p>
	  <p>Обновлено: <?= $updated?></p>
	  <p>Добавлено: <?= $inserted?></p>
    <p>Удалено: <?= $deleted?></p>
  </div>
</div>
