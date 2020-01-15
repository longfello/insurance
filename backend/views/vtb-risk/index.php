<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Страховые риски';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="risk-index">

    <p>
        <?php echo Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name' => [
              'attribute' => 'name',
              'format' => 'html',
              'value'     => function($risk){
                  /** @var $model \common\modules\ApiVtb\models\Risk */
	              $name    = $risk->name;
	              if ($internalRisks = $risk->internalRisks){
		              $name = "<span class='text-green'>$name.</span>";
		              $name .= "<span class='text-green'><br>Cоответствует внутренним рискам: <ul>";
		              foreach ($internalRisks as $internalRisk){
			              $rname = $internalRisk->name;
			              $rname.= $internalRisk->category ? " - {$internalRisk->category->name}" : "";
			              $name .= "<li>{$rname}</li>";
		              }
		              $name .= "</ul></span>";
	              } else {
		              $name = "<span class='text-red'>$name.</span>";
	                $name .= "<div class='text-red'>Нет соответствия рискам из внутреннего справочника</div>";
	              }
                  return $name;
              }
            ],
            'class',
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{update} {delete}'
            ],
        ],
    ]); ?>

</div>
