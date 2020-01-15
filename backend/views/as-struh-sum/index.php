<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Страховые суммы');
$this->params['breadcrumbs'][] = $this->title;

$programs = [];
foreach (\common\modules\ApiAlphaStrah\models\InsuranceProgramm::find()->all() as $program){
  /** @var $program \common\modules\ApiAlphaStrah\models\InsuranceProgramm */
	$programs[$program->insuranceProgrammID] = $program->insuranceProgrammPrintName;
}
?>
<div class="struh-sum-index">
    <p>
	    <?= Html::a(Yii::t('backend', 'Обновить страховые суммы'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'riskID',
            'risk',
//            'riskUID',
            'strahSummFrom',
            'strahSummTo',
            'valutaCode',
            'variant',
	        [
		        'class'=>\kartik\grid\EditableColumn::className(),
		        'attribute'=>'program_id',
		        'pageSummary'=>true,
		        'editableOptions'=> function ($model, $key, $index) use ($programs) {
			        return [
				        'size'   => \kartik\popover\PopoverX::SIZE_MEDIUM,
				        'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
				        'data' => $programs,
				        'displayValueConfig' => $programs,
			        ];
		        }
	        ],
	        [
		        'class'=>\kartik\grid\EditableColumn::className(),
		        'attribute'=>'premia',
		        'pageSummary'=>true,
		        'editableOptions'=> [
				        'size'   => \kartik\popover\PopoverX::SIZE_MEDIUM,
                'placement' => \kartik\popover\PopoverX::ALIGN_LEFT,
				        'inputType' => \kartik\editable\Editable::INPUT_TEXT
              ],
  	        ],

  	        ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>
</div>
