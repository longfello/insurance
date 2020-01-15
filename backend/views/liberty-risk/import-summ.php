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

$this->title = 'Обновление страховых сумм';
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Риски'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $risk_model['riskName'], 'url' => ['view','id'=>$risk_model['riskId']]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-import">
  <div class="well">
	  <p>Обновлено: <?= $updated?></p>
	  <p>Добавлено: <?= $inserted?></p>
      <p>Удалено: <?= $deleted?></p>
  </div>
  <p><?= Html::a(Yii::t('backend', 'Вернуться'), ['view','id'=>$risk_model['riskId']], ['class' => 'btn btn-primary']) ?></p>
</div>
