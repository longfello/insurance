<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Риски');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="risk-index">
    <p>
	    <?= Html::a(Yii::t('backend', 'Обновить риски'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'riskID',
            'risk',
			/*
	        [
		        'class'=>\kartik\grid\EditableColumn::className(),
            'hAlign' => \kartik\grid\GridView::ALIGN_LEFT,
		        'attribute'=>'parent_id',
		        'pageSummary'=>true,
  	        'refreshGrid' => true,
		        'editableOptions'=> function ($model, $key, $index) {
              $ids = explode(',', $model->parent_id);
              $models = \common\models\Risk::find()->where(['id' => $ids])->all();
              $names = [];
              foreach ($models as $model){
                $names[] = '<li>'.$model->name.'</li>';
              }
              $names = implode('', $names);
		          $names = $names?'<ul>'.$names.'</ul>':'(не задано)';

			        return [
			          'format' => \kartik\editable\Editable::FORMAT_BUTTON,
                'contentOptions' => [
                  'class' => 'risk-checkbox-list'
                ],
				        'size'   => \kartik\popover\PopoverX::SIZE_MEDIUM,
				        'inputType' => \kartik\editable\Editable::INPUT_CHECKBOX_LIST,
                'data' => \yii\helpers\ArrayHelper::map(\common\models\Risk::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name'),
                'displayValue' => $names,
//                'displayValueConfig' => \yii\helpers\ArrayHelper::map(\common\models\Risk::find()->asArray()->all(), 'id', 'name'),
			        ];
		        }
	        ],
			*/
	        [
		        'class'=>\kartik\grid\EditableColumn::className(),
		        'attribute'=>'enabled',
		        'pageSummary'=>true,
		        'editableOptions'=> function ($model, $key, $index) {
			        return [
				        'size'   => \kartik\popover\PopoverX::SIZE_MEDIUM,
				        'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
				        'data' => [
					        0 => 'Запрещен',
					        1 => 'Разрешен'
				        ],
				        'displayValueConfig' => [
					        0 => 'Запрещен',
					        1 => 'Разрешен'
				        ],
			        ];
		        }
	        ],
	        'riskPrintName:raw',
	        'riskUID',

	        ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>
</div>
