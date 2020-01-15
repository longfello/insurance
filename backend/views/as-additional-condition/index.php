<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Дополнительные условия');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="additional-condition-index">
    <p>
	    <?= Html::a(Yii::t('backend', 'Обновить список дополнительных условий'), ['import'], ['class' => 'btn btn-warning']) ?>
    </p>
	<?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'additionalConditionID',
	        [
		        'class'=>\kartik\grid\EditableColumn::className(),
		        'attribute'=>'parent_id',
		        'pageSummary'=>true,
		        'editableOptions'=> function ($model, $key, $index) {
			        return [
				        'size'   => \kartik\popover\PopoverX::SIZE_MEDIUM,
				        'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
				        'data' => [null => '(не задано)'] + \yii\helpers\ArrayHelper::map(\common\models\AdditionalCondition::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name'),
				        'displayValueConfig' => \yii\helpers\ArrayHelper::map(\common\models\AdditionalCondition::find()->asArray()->all(), 'id', 'name'),
			        ];
		        }
	        ],
            'additionalCondition',
            'additionalConditionUID',
            'additionalConditionValue',


	        ['class' => 'yii\grid\ActionColumn','template' => '{view}'],
        ],
    ]); ?>
</div>
